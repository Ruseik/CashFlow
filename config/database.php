<?php
return [
    'host' => 'localhost',
    'dbname' => 'budget_db',
    'username' => 'budget_user',
    'password' => 'asdw10010',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
