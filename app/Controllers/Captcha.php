<?php

namespace App\Controllers;
require_once __DIR__ . '/../../vendor/autoload.php';

use IconCaptcha\IconCaptcha;

class Captcha extends BaseController
{
    public function request()
    {
        try {
            // Start a session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Load the IconCaptcha options.
            $options = require_once __DIR__ . '/../Libraries/IconCaptcha/captcha-config.php';

            // Create an instance of IconCaptcha.
            $captcha = new IconCaptcha($options);

            // Handle the CORS preflight request.
            // * If you have disabled CORS in the configuration, you may remove this line.
            $captcha->handleCors();

            // Process the request.
            $result = $captcha->request()->process();

            // If we get here, the request was processed successfully
            return;

        } catch (\Throwable $exception) {



            // Return appropriate HTTP status code
            http_response_code(500);

            // Return JSON error response
            header('Content-Type: application/json');
            return $this->response->setJSON([
                'error' => 'Please complete the captcha verification.'
            ]);
        }
    }
}