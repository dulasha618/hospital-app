<?php
// Database connection (XAMPP defaults). Change here if your MySQL differs.
return [
    'db' => [
        'host'     => 'localhost',
        'name'     => 'hospital_db',
        'user'     => 'root',
        'password' => '',            // XAMPP default password is blank
        // Optional: full PDO DSN override (used only for testing)
        'dsn'      => getenv('HOSPITAL_DSN') ?: null,
    ],
    // Leave null to auto-detect (works in XAMPP sub-folders like /hospital-php).
    'base_url' => null,
    'low_stock_limit' => 10,
];
