<?php
declare(strict_types=1);

// Set error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Define base path constants
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);

// Autoload classes
spl_autoload_register(function ($class) {
    $file = BASE_PATH . '/src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Start session
session_start();

// Load database configuration
$dbConfig = require BASE_PATH . '/config/database.php';

// Initialize database connection
try {
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
    \Models\Model::setDB($pdo); // <-- ADD THIS LINE
} catch (PDOException $e) {
    // For production, log the error and show a user-friendly message
    error_log($e->getMessage());
    http_response_code(500);
    exit('Database connection failed. Please try again later.');
}

// Initialize router (we'll implement this later)
require BASE_PATH . '/src/router.php';
