<?php

namespace App\Controllers\Auth;

use CodeIgniter\Shield\Controllers\RegisterController as ShieldRegister;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Exceptions\ValidationException;
use CodeIgniter\Events\Events;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use App\Models\UserModel;

class RegisterController extends ShieldRegister
{
    /**
     * Displays the registration form.
     *
     * @return RedirectResponse|string
     */
    public function registerView()
    {
        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->registerRedirect());
        }

        // Check if registration is allowed
        if (!setting('Auth.allowRegistration')) {
            return redirect()->back()->withInput()
                ->with('error', lang('Auth.registerDisabled'));
        }

        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

        // If an action has been defined, start it up.
        if ($authenticator->hasAction()) {
            return redirect()->route('auth-action-show');
        }

        // Get referral ID from URL and store in session
        $referredBy = $this->request->getGet('ref');
        if ($referredBy) {
            session()->set('referred_by', $referredBy);
            log_message('debug', 'Stored referral ID in session: ' . $referredBy);
        }

        // Pass referral data to view
        $data = [
            'referred_by' => $referredBy
        ];

        return $this->view(setting('Auth.views')['register'], $data);
    }

    /**
     * Attempts to register the user.
     */
    public function registerAction(): RedirectResponse
    {
        if (auth()->loggedIn()) {
            return redirect()->to(config('Auth')->registerRedirect());
        }

        // Check if registration is allowed
        if (!setting('Auth.allowRegistration')) {
            return redirect()->back()->withInput()
                ->with('error', lang('Auth.registerDisabled'));
        }

        $users = $this->getUserProvider();

        // Validate here first, since some things,
        // like the password, can only be validated properly here.
        $rules = $this->getValidationRules();

        if (!$this->validateData($this->request->getPost(), $rules, [], config('Auth')->DBGroup)) {
            log_message('error', 'Registration validation failed: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Save the user
        $allowedPostFields = array_keys($rules);
        $user = $this->getUserEntity();

        // Get all post data including referred_by
        $postData = $this->request->getPost($allowedPostFields);

        // Handle referral separately since it might come from session if not in POST
        if (!isset($postData['referred_by']) || empty($postData['referred_by'])) {
            // Try to get from session if it was set during registerView
            $postData['referred_by'] = session()->get('referred_by');
        }

        // Log the final data that will be saved
        $logData = [
            'email' => $postData['email'],
            'username' => $postData['username'],
            'referred_by' => $postData['referred_by'] ?? null
        ];
        log_message('debug', 'New registration attempt with final data: ' . json_encode($logData));

        $user->fill($postData);

        // Workaround for email only registration/login
        if ($user->username === null) {
            $user->username = null;
        }

        try {
            $users->save($user);
        } catch (ValidationException $e) {
            log_message('error', 'Registration save failed: ' . json_encode($users->errors()));
            return redirect()->back()->withInput()->with('errors', $users->errors());
        }

        // To get the complete user object with ID, we need to get from the database
        $user = $users->findById($users->getInsertID());
        log_message('info', 'User registered successfully. User ID: ' . $user->id);

        // Add to default group
        $users->addToDefaultGroup($user);
        log_message('debug', 'User added to default group. User ID: ' . $user->id);

        Events::trigger('register', $user);

        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();

        $authenticator->startLogin($user);

        // If an action has been defined for register, start it up.
        $hasAction = $authenticator->startUpAction('register', $user);
        if ($hasAction) {
            log_message('debug', 'Registration requires additional action. User ID: ' . $user->id);
            return redirect()->route('auth-action-show');
        }

        // Set the user active
        $user->activate();
        log_message('debug', 'User account activated. User ID: ' . $user->id);

        $authenticator->completeLogin($user);

        // Success!
        return redirect()->to(config('Auth')->registerRedirect())
            ->with('message', lang('Auth.registerSuccess'));
    }

    /**
     * Returns the rules that should be used for validation.
     */
    protected function getValidationRules(): array
    {
        $rules = parent::getValidationRules();

        // Add our custom referred_by rule
        $rules['referred_by'] = [
            'label' => 'Referred By',
            'rules' => [
                'permit_empty',
                'integer',
                'is_not_unique[users.id]',
            ],
        ];

        return $rules;
    }
}