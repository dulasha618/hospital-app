<?php
// Front controller: every request comes through here (see .htaccess)
if (PHP_VERSION_ID < 70100) {
    exit('<h3>This project needs PHP 7.1 or newer.</h3><p>Your PHP version is ' . PHP_VERSION
        . '. Please install the latest XAMPP (PHP 8.x) from apachefriends.org.</p>');
}
require __DIR__ . '/app/bootstrap.php';
