<?php
require_once 'models/User.php';

class AjaxController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Check if username already exists
     */
    public function checkUsername()
    {
        // Get username from request
        $username = isset($_GET['username']) ? trim($_GET['username']) : '';

        // Initialize response
        $response = [
            'exists' => false
        ];

        // Check if username is not empty
        if (!empty($username)) {
            // Check if username exists in database
            $user = $this->userModel->getUserByUsername($username);
            if ($user) {
                $response['exists'] = true;
            }
        }

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    /**
     * Check if email already exists
     */
    public function checkEmail()
    {
        // Get email from request
        $email = isset($_GET['email']) ? trim($_GET['email']) : '';

        // Initialize response
        $response = [
            'exists' => false
        ];

        // Check if email is not empty and valid
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Check if email exists in database
            $user = $this->userModel->getUserByEmail($email);
            if ($user) {
                $response['exists'] = true;
            }
        }

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
