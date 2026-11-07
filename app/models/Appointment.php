<?php
class Appointment
{
    const STATUSES = ['Scheduled', 'Completed', 'Cancelled'];

    private const SELECT = 'SELECT a.id, a.patientId, a.doctorId, a.`date`, a.`time`, a.reason, a.status,
               p.fullName AS patientName, d.fullName AS doctorName
        FROM appointments a
        LEFT JOIN users p ON p.id = a.patientId
        LEFT JOIN users d ON d.id = a.doctorId';

    public static function getAll(array $f = []): array
    {
        $where = []; $params = [];
        if (!empty($f['patientId'])) { $where[] = 'a.patientId = ?'; $params[] = $f['patientId']; }
        if (!empty($f['doctorId']))  { $where[] = 'a.doctorId = ?';  $params[] = $f['doctorId']; }
        if (!empty($f['q'])) {
            $like = '%' . $f['q'] . '%';
            $where[] = '(p.fullName LIKE ? OR d.fullName LIKE ? OR a.reason LIKE ? OR a.status LIKE ?)';
            array_push($params, $like, $like, $like, $like);
        }
        $sql = self::SELECT . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY a.`date` DESC, a.`time` DESC';
        return Database::all($sql, $params);
    }

    public static function findById(?int $id): ?array
    {
        return $id ? Database::one(self::SELECT . ' WHERE a.id = ?', [$id]) : null;
    }

    // Is this doctor already booked at that date/time?
    public static function isSlotTaken(int $doctorId, string $date, string $time, int $excludeId = 0): bool
    {
        return (bool) Database::one(
            "SELECT id FROM appointments WHERE doctorId = ? AND `date` = ? AND `time` = ? AND status <> 'Cancelled' AND id <> ?",
            [$doctorId, $date, $time, $excludeId]
        );
    }

    public static function create(array $d): void
    {
        Database::run(
            'INSERT INTO appointments (patientId, doctorId, `date`, `time`, reason) VALUES (?, ?, ?, ?, ?)',
            [$d['patientId'], $d['doctorId'], $d['date'], $d['time'], $d['reason']]
        );
    }

    public static function update(int $id, array $d): void
    {
        Database::run(
            'UPDATE appointments SET patientId = ?, doctorId = ?, `date` = ?, `time` = ?, reason = ?, status = ? WHERE id = ?',
            [$d['patientId'], $d['doctorId'], $d['date'], $d['time'], $d['reason'], $d['status'], $id]
        );
    }

    public static function setStatus(int $id, string $status): void
    {
        Database::run('UPDATE appointments SET status = ? WHERE id = ?', [$status, $id]);
    }

    public static function remove(int $id): void
    {
        Database::run('DELETE FROM appointments WHERE id = ?', [$id]);
    }
}
