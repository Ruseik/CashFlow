<?php
declare(strict_types=1);

class Router {
    private array $routes = [];
    private ?string $matchedRoute = null;
    
    public function addRoute(string $method, string $path, array $handler): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            $params = [];
            if ($route['method'] === $method && $this->matchRoute($route['path'], $path, $params)) {
                $this->matchedRoute = $route['path'];
                [$controller, $action] = $route['handler'];
                $instance = new $controller();
                // Cast numeric params to int
                foreach ($params as &$param) {
                    if (is_numeric($param)) {
                        $param = (int)$param;
                    }
                }
                unset($param);
                call_user_func_array([$instance, $action], $params);
                return;
            }
        }
        
        // No route found
        http_response_code(404);
        require BASE_PATH . '/src/Views/404.php';
    }
    
    private function matchRoute(string $routePath, string $requestPath, array &$params = []): bool {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));
        
        if (count($routeParts) !== count($requestParts)) {
            return false;
        }
        
        $params = [];
        for ($i = 0; $i < count($routeParts); $i++) {
            if (strpos($routeParts[$i], ':') === 0) {
                $params[] = $requestParts[$i]; // Use numeric array for call_user_func_array
                continue;
            }
            
            if ($routeParts[$i] !== $requestParts[$i]) {
                return false;
            }
        }
        
        return true;
    }
}

// Initialize router
$router = new Router();

// Define routes
$router->addRoute('GET', '/', ['Controllers\HomeController', 'index']);
$router->addRoute('GET', '/login', ['Controllers\AuthController', 'loginForm']);
$router->addRoute('POST', '/login', ['Controllers\AuthController', 'login']);
$router->addRoute('GET', '/register', ['Controllers\AuthController', 'registerForm']);
$router->addRoute('POST', '/register', ['Controllers\AuthController', 'register']);
$router->addRoute('GET', '/logout', ['Controllers\AuthController', 'logout']);

// Transaction routes
$router->addRoute('GET', '/transactions', ['Controllers\TransactionController', 'index']);
$router->addRoute('GET', '/transactions/create', ['Controllers\TransactionController', 'create']);
$router->addRoute('POST', '/transactions', ['Controllers\TransactionController', 'store']);
$router->addRoute('GET', '/transactions/:id', ['Controllers\TransactionController', 'show']);
$router->addRoute('GET', '/transactions/:id/edit', ['Controllers\TransactionController', 'edit']);
$router->addRoute('POST', '/transactions/:id', ['Controllers\TransactionController', 'update']);
$router->addRoute('POST', '/transactions/:id/delete', ['Controllers\TransactionController', 'delete']);

// Entity routes
$router->addRoute('GET', '/entities', ['Controllers\EntityController', 'index']);
$router->addRoute('GET', '/entities/create', ['Controllers\EntityController', 'create']);
$router->addRoute('POST', '/entities', ['Controllers\EntityController', 'store']);
$router->addRoute('GET', '/entities/:id/edit', ['Controllers\EntityController', 'edit']);
$router->addRoute('POST', '/entities/:id', ['Controllers\EntityController', 'update']);
$router->addRoute('POST', '/entities/:id/delete', ['Controllers\EntityController', 'delete']);

// Currency routes
$router->addRoute('GET', '/currencies', ['Controllers\CurrencyController', 'index']);
$router->addRoute('GET', '/currencies/create', ['Controllers\CurrencyController', 'create']);
$router->addRoute('POST', '/currencies', ['Controllers\CurrencyController', 'store']);
$router->addRoute('GET', '/currencies/:id/edit', ['Controllers\CurrencyController', 'edit']);
$router->addRoute('POST', '/currencies/:id', ['Controllers\CurrencyController', 'update']);
$router->addRoute('POST', '/currencies/:id/delete', ['Controllers\CurrencyController', 'delete']);

// Purpose routes
$router->addRoute('GET', '/purposes', ['Controllers\PurposeController', 'index']);
$router->addRoute('GET', '/purposes/create', ['Controllers\PurposeController', 'create']);
$router->addRoute('POST', '/purposes', ['Controllers\PurposeController', 'store']);
$router->addRoute('GET', '/purposes/:id/edit', ['Controllers\PurposeController', 'edit']);
$router->addRoute('POST', '/purposes/:id', ['Controllers\PurposeController', 'update']);
$router->addRoute('POST', '/purposes/:id/delete', ['Controllers\PurposeController', 'delete']);

// Mode routes
$router->addRoute('GET', '/modes', ['Controllers\ModeController', 'index']);
$router->addRoute('GET', '/modes/create', ['Controllers\ModeController', 'create']);
$router->addRoute('POST', '/modes', ['Controllers\ModeController', 'store']);
$router->addRoute('GET', '/modes/:id/edit', ['Controllers\ModeController', 'edit']);
$router->addRoute('POST', '/modes/:id', ['Controllers\ModeController', 'update']);
$router->addRoute('POST', '/modes/:id/delete', ['Controllers\ModeController', 'delete']);

// Analytics routes
$router->addRoute('GET', '/analytics', ['Controllers\AnalyticsController', 'index']);
$router->addRoute('GET', '/analytics/expenditure', ['Controllers\AnalyticsController', 'expenditure']);

// Task routes
$router->addRoute('GET', '/tasks', ['Controllers\TaskController', 'index']);
$router->addRoute('POST', '/tasks', ['Controllers\TaskController', 'store']);
$router->addRoute('GET', '/tasks/:id', ['Controllers\TaskController', 'show']);
$router->addRoute('POST', '/tasks/:id/success', ['Controllers\TaskController', 'markSuccessful']);
$router->addRoute('POST', '/tasks/:id/fail', ['Controllers\TaskController', 'markFailed']);

// Dispatch the request
$router->dispatch();
