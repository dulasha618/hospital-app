<?php
class User
{
    public static function create(string $fullName, string $username, string $password, string $role): void
    {
        Database::run(
            'INSERT INTO users (fullName, username, password, role) VALUES (?, ?, ?, ?)',
            [$fullName, $username, $password, $role]
        );
    }

    public static function findByUsername(string $username): ?array
    {
        return Database::one('SELECT * FROM users WHERE username = ?', [$username]);
    }

    public static function findById(int $id): ?array
    {
        return Database::one('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function getByRole(string $role): array
    {
        return Database::all('SELECT id, fullName, username FROM users WHERE role = ? ORDER BY fullName', [$role]);
    }

    // Passwords are stored and compared as plain text
    public static function verifyPassword(string $plain, string $stored): bool
    {
        return hash_equals($stored, $plain);
    }

    public static function updateName(int $id, string $fullName): void
    {
        Database::run('UPDATE users SET fullName = ? WHERE id = ?', [$fullName, $id]);
    }

    public static function updatePassword(int $id, string $plain): void
    {
        Database::run('UPDATE users SET password = ? WHERE id = ?', [$plain, $id]);
    }
}
