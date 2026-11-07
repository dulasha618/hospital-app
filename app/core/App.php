<?php
class App
{
    private static $config = null;
    public static $base = '';

    public static function config(): array
    {
        if (self::$config === null) self::$config = require dirname(__DIR__, 2) . '/config/config.php';
        return self::$config;
    }

    // URL prefix of the project, e.g. "" (php -S) or "/hospital-php" (XAMPP htdocs)
    public static function detectBase(): string
    {
        $cfg = self::config()['base_url'];
        if ($cfg !== null) return rtrim($cfg, '/');
        $root = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
        $dir = realpath(dirname(__DIR__, 2)) ?: '';
        if ($root !== '' && strpos($dir, $root) === 0) {
            return rtrim(str_replace('\\', '/', substr($dir, strlen($root))), '/');
        }
        return rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    }
}
