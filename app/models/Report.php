<?php
// Read-only aggregate queries used by the Reports page (business logic lives here, not in the view)
class Report
{
    // Revenue per month for the last $months months (months with no bills are filled with 0)
    public static function monthlyRevenue(int $months): array
    {
        $months = max(1, min(24, $months));
        $start = date('Y-m-01', strtotime('first day of -' . ($months - 1) . ' month')) . ' 00:00:00';
        $rows = Database::all(
            "SELECT DATE_FORMAT(createdAt, '%Y-%m') AS ym,
                    SUM(CASE WHEN status = 'Paid' THEN total ELSE 0 END)   AS paid,
                    SUM(CASE WHEN status = 'Unpaid' THEN total ELSE 0 END) AS unpaid
             FROM bills WHERE createdAt >= ? GROUP BY ym",
            [$start]
        );
        $byMonth = [];
        foreach ($rows as $r) $byMonth[$r['ym']] = $r;

        $out = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $key = date('Y-m', strtotime("first day of -$i month"));
            $out[] = [
                'month'  => $key,
                'label'  => date('M y', strtotime($key . '-01')),
                'paid'   => (float) ($byMonth[$key]['paid'] ?? 0),
                'unpaid' => (float) ($byMonth[$key]['unpaid'] ?? 0),
            ];
        }
        return $out;
    }

    // Appointment counts per status (optionally for one doctor)
    public static function appointmentsByStatus(?int $doctorId = null): array
    {
        $sql = 'SELECT status, COUNT(*) AS c FROM appointments' . ($doctorId ? ' WHERE doctorId = ?' : '') . ' GROUP BY status';
        $rows = Database::all($sql, $doctorId ? [$doctorId] : []);
        $counts = array_fill_keys(Appointment::STATUSES, 0);
        foreach ($rows as $r) $counts[$r['status']] = (int) $r['c'];
        return $counts;
    }

    // Workload of every doctor (all appointments except cancelled ones)
    public static function appointmentsPerDoctor(): array
    {
        return Database::all(
            "SELECT d.fullName AS label, COUNT(a.id) AS c
             FROM users d LEFT JOIN appointments a ON a.doctorId = d.id AND a.status <> 'Cancelled'
             WHERE d.role = 'Doctor' GROUP BY d.id, d.fullName ORDER BY c DESC, d.fullName"
        );
    }

    // Headline money numbers
    public static function billTotals(): array
    {
        $r = Database::one(
            "SELECT COALESCE(SUM(CASE WHEN status = 'Paid' THEN total END), 0)   AS paid,
                    COALESCE(SUM(CASE WHEN status = 'Unpaid' THEN total END), 0) AS unpaid,
                    COUNT(*) AS bills
             FROM bills"
        );
        return ['paid' => (float) $r['paid'], 'unpaid' => (float) $r['unpaid'], 'bills' => (int) $r['bills']];
    }
}
