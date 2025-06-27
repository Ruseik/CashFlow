<?php
return [
    'app' => [
        'name' => 'Cash Flow Management Platform',
        'url' => 'http://localhost',  // Change this in production
        'env' => 'development',  // Change to 'production' in production
        'debug' => true,  // Set to false in production
        'timezone' => 'Asia/Colombo',
        'locale' => 'en',
        'key' => bin2hex(random_bytes(32)),  // Generate a new key for production
    ],
    
    'session' => [
        'lifetime' => 7200,  // 2 hours
        'path' => '/',
        'domain' => '',
        'secure' => false,  // Set to true in production with HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ],
    
    'security' => [
        'password_min_length' => 8,
        'password_require_special' => true,
        'password_require_number' => true,
        'password_require_uppercase' => true,
        'password_require_lowercase' => true,
        'max_login_attempts' => 5,
        'lockout_time' => 900,  // 15 minutes
    ],
    
    'pagination' => [
        'per_page' => 10,
        'max_per_page' => 100
    ],
    
    'logging' => [
        'path' => BASE_PATH . '/logs',
        'level' => 'debug'  // Set to 'error' in production
    ],
    
    'defaults' => [
        'currency' => [
            'code' => 'LKR',
            'name' => 'Sri Lankan Rupee',
            'symbol' => 'Rs.'
        ],
        'entity' => [
            'void' => [
                'name' => 'Void',
                'description' => 'System entity for external transactions'
            ]
        ]
    ]
];
