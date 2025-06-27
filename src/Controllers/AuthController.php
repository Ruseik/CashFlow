<?php
declare(strict_types=1);

namespace Controllers;

use Models\User;

class AuthController extends Controller {
    private User $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function loginForm(): void {
        if ($this->isAuthenticated()) {
            $this->redirect('/');
        }
        
        $this->render('auth/login', [
            'csrf_token' => $this->csrf()
        ]);
    }
    
    public function login(): void {
        $this->validateCsrf();
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $user = $this->userModel->findByUsername($username);
        
        if (!$user || !User::verifyPassword($password, $user['password'])) {
            $_SESSION['error'] = 'Invalid username or password';
            $this->redirect('/login');
            return;
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        // Redirect to intended page or home
        $redirect = $_SESSION['redirect_after_login'] ?? '/';
        unset($_SESSION['redirect_after_login']);
        $this->redirect($redirect);
    }
    
    public function registerForm(): void {
        if ($this->isAuthenticated()) {
            $this->redirect('/');
        }
        
        $this->render('auth/register', [
            'csrf_token' => $this->csrf()
        ]);
    }
    
    public function register(): void {
        $this->validateCsrf();
        
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // Validation
        $errors = [];
        
        if (strlen($username) < 3) {
            $errors['username'] = 'Username must be at least 3 characters long';
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email address';
        }
        
        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long';
        }
        
        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Passwords do not match';
        }
        
        // Check if username exists
        if ($this->userModel->findByUsername($username)) {
            $errors['username'] = 'Username already exists';
        }
        
        // Check if email exists
        if ($this->userModel->findByEmail($email)) {
            $errors['email'] = 'Email already exists';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = [
                'username' => $username,
                'email' => $email
            ];
            $this->redirect('/register');
            return;
        }
        
        // Create user
        $userId = $this->userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => User::hashPassword($password),
            'primary_currency_id' => 1, // Default to LKR
            'is_admin' => false
        ]);
        
        if (!$userId) {
            $_SESSION['error'] = 'Failed to create account';
            $this->redirect('/register');
            return;
        }
        
        $_SESSION['success'] = 'Account created successfully. Please log in.';
        $this->redirect('/login');
    }
    
    public function logout(): void {
        session_destroy();
        $this->redirect('/login');
    }
}
