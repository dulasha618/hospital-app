<?php
class Medicine
{
    public static function limit(): int { return (int) App::config()['low_stock_limit']; }

    public static function getAll(string $q = ''): array
    {
        if ($q !== '') return Database::all('SELECT * FROM medicines WHERE name LIKE ? ORDER BY name', ['%' . $q . '%']);
        return Database::all('SELECT * FROM medicines ORDER BY name');
    }

    public static function getLowStock(): array
    {
        return Database::all('SELECT * FROM medicines WHERE stock < ? ORDER BY stock', [self::limit()]);
    }

    public static function findById(?int $id): ?array
    {
        return $id ? Database::one('SELECT * FROM medicines WHERE id = ?', [$id]) : null;
    }

    public static function create(string $name, int $stock): void
    {
        Database::run('INSERT INTO medicines (name, stock) VALUES (?, ?)', [$name, $stock]);
    }

    public static function update(int $id, string $name, int $stock): void
    {
        Database::run('UPDATE medicines SET name = ?, stock = ? WHERE id = ?', [$name, $stock, $id]);
    }

    public static function remove(int $id): void
    {
        Database::run('DELETE FROM medicines WHERE id = ?', [$id]);
    }
}
