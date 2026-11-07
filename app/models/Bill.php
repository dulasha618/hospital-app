<?php
class Bill
{
    const STATUSES = ['Unpaid', 'Paid'];

    private const SELECT = 'SELECT b.id, b.patientId, b.consultationFee, b.medicineFee, b.total, b.status, b.createdAt,
               p.fullName AS patientName
        FROM bills b
        LEFT JOIN users p ON p.id = b.patientId';

    public static function getAll(array $f = []): array
    {
        $where = []; $params = [];
        if (!empty($f['patientId'])) { $where[] = 'b.patientId = ?'; $params[] = $f['patientId']; }
        if (!empty($f['q'])) {
            $like = '%' . $f['q'] . '%';
            $where[] = '(p.fullName LIKE ? OR b.status LIKE ?)';
            array_push($params, $like, $like);
        }
        return Database::all(self::SELECT . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY b.id DESC', $params);
    }

    public static function findById(?int $id): ?array
    {
        return $id ? Database::one(self::SELECT . ' WHERE b.id = ?', [$id]) : null;
    }

    public static function create(array $d): void
    {
        $total = round($d['consultationFee'] + $d['medicineFee'], 2);
        Database::run(
            'INSERT INTO bills (patientId, consultationFee, medicineFee, total) VALUES (?, ?, ?, ?)',
            [$d['patientId'], $d['consultationFee'], $d['medicineFee'], $total]
        );
    }

    public static function update(int $id, array $d): void
    {
        $total = round($d['consultationFee'] + $d['medicineFee'], 2);
        Database::run(
            'UPDATE bills SET patientId = ?, consultationFee = ?, medicineFee = ?, total = ?, status = ? WHERE id = ?',
            [$d['patientId'], $d['consultationFee'], $d['medicineFee'], $total, $d['status'], $id]
        );
    }

    public static function setStatus(int $id, string $status): void
    {
        Database::run('UPDATE bills SET status = ? WHERE id = ?', [$status, $id]);
    }

    public static function remove(int $id): void
    {
        Database::run('DELETE FROM bills WHERE id = ?', [$id]);
    }
}
