<?php
class Prescription
{
    private const SELECT = 'SELECT pr.id, pr.patientId, pr.doctorId, pr.diagnosis, pr.medicines, pr.createdAt,
               p.fullName AS patientName, d.fullName AS doctorName
        FROM prescriptions pr
        LEFT JOIN users p ON p.id = pr.patientId
        LEFT JOIN users d ON d.id = pr.doctorId';

    public static function getAll(array $f = []): array
    {
        $where = []; $params = [];
        if (!empty($f['patientId'])) { $where[] = 'pr.patientId = ?'; $params[] = $f['patientId']; }
        if (!empty($f['doctorId']))  { $where[] = 'pr.doctorId = ?';  $params[] = $f['doctorId']; }
        if (!empty($f['q'])) {
            $like = '%' . $f['q'] . '%';
            $where[] = '(p.fullName LIKE ? OR d.fullName LIKE ? OR pr.diagnosis LIKE ? OR pr.medicines LIKE ?)';
            array_push($params, $like, $like, $like, $like);
        }
        return Database::all(self::SELECT . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY pr.id DESC', $params);
    }

    public static function findById(?int $id): ?array
    {
        return $id ? Database::one(self::SELECT . ' WHERE pr.id = ?', [$id]) : null;
    }

    public static function create(array $d): void
    {
        Database::run(
            'INSERT INTO prescriptions (patientId, doctorId, diagnosis, medicines) VALUES (?, ?, ?, ?)',
            [$d['patientId'], $d['doctorId'], $d['diagnosis'], $d['medicines']]
        );
    }

    public static function update(int $id, array $d): void
    {
        Database::run(
            'UPDATE prescriptions SET patientId = ?, diagnosis = ?, medicines = ? WHERE id = ?',
            [$d['patientId'], $d['diagnosis'], $d['medicines'], $id]
        );
    }

    public static function remove(int $id): void
    {
        Database::run('DELETE FROM prescriptions WHERE id = ?', [$id]);
    }
}
