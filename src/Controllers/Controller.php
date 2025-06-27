<?php
declare(strict_types=1);

namespace Controllers;

abstract class Controller {
    protected function render(string $view, array $data = []): void {
        // Extract data to make variables available in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        require BASE_PATH . "/src/Views/{$view}.php";
        
        // Get the contents and clean the buffer
        $content = ob_get_clean();
        
        // Include the layout
        require BASE_PATH . '/src/Views/layout.php';
    }
    
    protected function redirect(string $path): void {
        header("Location: {$path}");
        exit;
    }
    
    protected function json(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function isAuthenticated(): bool {
        return isset($_SESSION['user_id']);
    }
    
    protected function requireAuth(): void {
        if (!$this->isAuthenticated()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->redirect('/login');
        }
    }
    
    protected function csrf(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    protected function validateCsrf(): void {
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            (
                empty($_POST['csrf_token']) ||
                empty($_SESSION['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
            )
        ) {
            http_response_code(403);
            exit('CSRF token validation failed');
        }
    }
}
