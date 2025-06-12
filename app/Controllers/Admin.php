<?php

namespace App\Controllers;
use App\Models\WithdrawalModel;
use App\Models\UserModel;

class Admin extends BaseController
{
    protected $withdrawalModel;
    protected $userModel;
    public function __construct()
    {
        $this->withdrawalModel = new WithdrawalModel();
        $this->userModel = new UserModel();
    }
    public function index(): string
    {
        $total_users = count($this->userModel->getAllUsers() ?? 0);
        $total_withdrawals = count(
            $this->withdrawalModel->getAllWithdrawals() ?? 0
        );
        $data = [
            "total_users" => $total_users,
            "total_withdrawals" => $total_withdrawals,
            "title" => "Admin Dashboard",
        ];

        return view("admin/index", $data);
    }

    public function profile(): string
    {
        $user = auth()->user();

        $data = [
            "title" => "Profile",
            "user" => $user,
        ];

        return view("admin/profile/index", $data);
    }

    public function manageUsers(): string
    {
        $user = auth()->user();

        //Get only users in 'user' group
        $users = $this->userModel->getAllUsers();

        // Process users to include ban status
        $processedUsers = [];
        $userProvider = auth()->getProvider();

        foreach ($users as $userData) {
            $userEntity = $userProvider->findById($userData["id"]);
            $userData["is_banned"] = $userEntity
                ? $userEntity->isBanned()
                : false;
            $userData["is_active"] = $userEntity
                ? $userEntity->isActivated()
                : false;
            $userData["ban_reason"] = str_replace(
                "You are banned. Reason : ",
                "",
                $userEntity ? $userEntity->getBanMessage() : false
            );

            // If using getAllUsersWithGroups(), you can also display the groups:
            // $userData['user_groups'] = $userData['groups'] ?? 'No groups';

            $processedUsers[] = $userData;
        }

        $data = [
            "title" => "Manage Users",

            "users" => $processedUsers,
        ];

        return view("admin/manage-users/index", $data);
    }

    public function manageWithdrawals(): string
    {
        $withdrawals = $this->withdrawalModel->getAllWithdrawals();

        $data = [
            "title" => "Manage Withdrawals",
            "withdrawals" => $withdrawals,
        ];

        return view("admin/manage-withdrawals/index", $data);
    }

    public function banUser(int $userId): \CodeIgniter\HTTP\RedirectResponse
    {
        try {
            // Get the user provider
            $userProvider = auth()->getProvider();

            // Find the user by ID
            $user = $userProvider->findById($userId);

            if (!$user) {
                return redirect()
                    ->to("admin/manage-users")
                    ->with("error", "User not found.");
            }

            // Check if user is already banned
            if ($user->isBanned()) {
                return redirect()
                    ->to("admin/manage-users")
                    ->with("warning", "User is already banned.");
            }

            // Ban the user with a reason
            $user->ban("Banned by administrator");

            return redirect()
                ->to("admin/manage-users")
                ->with("success", "User has been banned successfully.");
        } catch (\Exception $e) {
            log_message("error", "Error banning user: " . $e->getMessage());
            return redirect()
                ->to("admin/manage-users")
                ->with("error", "An error occurred while banning the user.");
        }
    }

    public function unbanUser(int $userId): \CodeIgniter\HTTP\RedirectResponse
    {
        try {
            // Get the user provider
            $userProvider = auth()->getProvider();

            // Find the user by ID
            $user = $userProvider->findById($userId);

            if (!$user) {
                return redirect()
                    ->to("admin/manage-users")
                    ->with("error", "User not found.");
            }

            // Check if user is actually banned
            if (!$user->isBanned()) {
                return redirect()
                    ->to("admin/manage-users")
                    ->with("warning", "User is not banned.");
            }

            // Unban the user
            $user->unBan();

            return redirect()
                ->to("admin/manage-users")
                ->with("success", "User has been unbanned successfully.");
        } catch (\Exception $e) {
            log_message("error", "Error unbanning user: " . $e->getMessage());
            return redirect()
                ->to("admin/manage-users")
                ->with("error", "An error occurred while unbanning the user.");
        }
    }
    public function editUser(int $userId)
    {
        // Load Form Helper
        helper(["form", "url"]);

        try {
            $userProvider = auth()->getProvider();
            $user = $userProvider->findById($userId);

            if (!$user) {
                return redirect()
                    ->to("admin/manage-users")
                    ->with("error", "User not found.");
            }

            // Get user's email from auth_identities
            $identityModel = model(
                "CodeIgniter\Shield\Models\UserIdentityModel"
            );
            $identity = $identityModel
                ->where("user_id", $userId)
                ->where("type", "email_password")
                ->first();

            $data = [
                "title" => "Edit User",
                "userId" => $userId,
                "user" => $user,
                "username" => old("username", $user->username),
                "email" => old("email", $identity ? $identity->secret : ""),
                "validation" => \Config\Services::validation(),
            ];

            return view("admin/manage-users/edit", $data);
        } catch (\Exception $e) {
            log_message(
                "error",
                "Error loading edit user form: " . $e->getMessage()
            );
            return redirect()
                ->to("admin/manage-users")
                ->with(
                    "error",
                    "An error occurred while loading the user data."
                );
        }
    }

    public function updateUser(int $userId)
    {
        // Load Form Helper
        helper(["form", "url"]);

        // Check if this is an AJAX request
        $isAjax = $this->request->isAJAX();

        try {
            $userProvider = auth()->getProvider();
            $user = $userProvider->findById($userId);

            if (!$user) {
                if ($isAjax) {
                    return $this->response->setJSON([
                        "success" => false,
                        "error" => true,
                        "message" => "User not found",
                    ]);
                }
                return redirect()
                    ->to("admin/manage-users/edit/" . $userId)
                    ->with("error", "User not found")
                    ->withInput();
            }

            // Get current user's email identity for validation exclusion
            $identityModel = model(
                "CodeIgniter\Shield\Models\UserIdentityModel"
            );
            $currentIdentity = $identityModel
                ->where("user_id", $userId)
                ->where("type", "email_password")
                ->first();

            $userData = $this->request->getPost();

            $rules = [
                "username" => [
                    "label" => "Username",
                    "rules" =>
                        "required|max_length[30]|min_length[3]|regex_match[/\A[a-zA-Z0-9\.]+\z/]|is_unique[users.username,id," .
                        $userId .
                        "]",
                    "errors" => [
                        "required" => "Username is required.",
                        "is_unique" => "Username is already taken.",
                        "regex_match" =>
                            "Username can only contain letters, numbers, and dots.",
                        "max_length" => "Username cannot exceed 30 characters.",
                        "min_length" =>
                            "Username must be at least 3 characters long.",
                    ],
                ],
                "email" => [
                    "label" => "Email",
                    "rules" =>
                        "required|max_length[254]|valid_email|is_unique[auth_identities.secret,id," .
                        ($currentIdentity ? $currentIdentity->id : 0) .
                        "]",
                    "errors" => [
                        "required" => "Email is required.",
                        "is_unique" => "Email is already registered.",
                        "valid_email" => "Please enter a valid email address.",
                        "max_length" => "Email cannot exceed 254 characters.",
                    ],
                ],
            ];

            // Validate input data
            if (!$this->validate($rules)) {
                if ($isAjax) {
                    return $this->response->setJSON([
                        "success" => false,
                        "error" => true,
                        "message" => implode(
                            "<br>",
                            $this->validator->getErrors()
                        ),
                    ]);
                }
                return redirect()
                    ->to("admin/manage-users/edit/" . $userId)
                    ->withInput()
                    ->with("errors", $this->validator->getErrors());
            }

            // Update username in users table
            $user->username = $userData["username"];
            $updateResult = $userProvider->save($user);

            if (!$updateResult) {
                if ($isAjax) {
                    return $this->response->setJSON([
                        "success" => false,
                        "error" => true,
                        "message" => "Failed to update username",
                    ]);
                }
                return redirect()
                    ->to("admin/manage-users/edit/" . $userId)
                    ->withInput()
                    ->with("error", "Failed to update username");
            }

            // Update email in auth_identities table if it changed
            if (
                $currentIdentity &&
                $currentIdentity->secret !== $userData["email"]
            ) {
                $currentIdentity->secret = $userData["email"];
                $identityModel->save($currentIdentity);
            }

            if ($isAjax) {
                return $this->response->setJSON([
                    "success" => true,
                    "message" => "User updated successfully",
                ]);
            }

            return redirect()
                ->to("admin/manage-users")
                ->with("success", "User updated successfully");
        } catch (\Exception $e) {
            log_message("error", "Error updating user: " . $e->getMessage());

            if ($isAjax) {
                return $this->response->setJSON([
                    "success" => false,
                    "error" => true,
                    "message" =>
                        "An error occurred while updating the user: " .
                        $e->getMessage(),
                ]);
            }

            return redirect()
                ->to("admin/manage-users/edit/" . $userId)
                ->withInput()
                ->with("error", "An error occurred while updating the user");
        }
    }
}
