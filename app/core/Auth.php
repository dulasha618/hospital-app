<?php
// Middleware: each returns true to continue, or sends a response and returns false
class Auth
{
    public static function requireLogin(): bool
    {
        if (!currentUser()) { header('Location: ' . url('/login')); return false; }
        // Stop the browser showing protected pages from cache after logout
        header('Cache-Control: no-store');
        return true;
    }

    public static function requireRole(...$roles)
    {
        return function () use ($roles): bool {
            $u = currentUser();
            if (!$u) { header('Location: ' . url('/login')); return false; }
            if (!in_array($u['role'], $roles, true)) {
                (new Controller())->renderDenied($u['role']);
                return false;
            }
            header('Cache-Control: no-store');
            return true;
        };
    }

    // Logged-in users shouldn't see login/register again
    public static function guest(): bool
    {
        if (currentUser()) { header('Location: ' . url('/dashboard')); return false; }
        return true;
    }
}
