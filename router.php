<?php
// Only for `php -S localhost:8000 router.php` (XAMPP/Apache uses .htaccess instead)
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path !== '/' && is_file(__DIR__ . $path)) return false; // serve css etc.
require __DIR__ . '/index.php';
