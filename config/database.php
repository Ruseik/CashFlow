<?php
return [
    'host' => 'localhost',
    'dbname' => 'your database name',
    'username' => 'your database username',
    'password' => 'your password for the database user',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
