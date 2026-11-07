<?php
// Small number tiles shown on each role's dashboard
class Stats
{
    public static function forUser(array $user): array
    {
        $low = Medicine::limit();
        $today = today();
        $id = $user['id'];

        if ($user['role'] === 'Patient') {
            return [
                ['label' => 'Upcoming Appointments', 'tone' => 'info', 'value' => Database::count("SELECT COUNT(*) AS c FROM appointments WHERE patientId = ? AND status = 'Scheduled' AND `date` >= ?", [$id, $today])],
                ['label' => 'My Prescriptions', 'tone' => 'success', 'value' => Database::count('SELECT COUNT(*) AS c FROM prescriptions WHERE patientId = ?', [$id])],
                ['label' => 'Unpaid Bills', 'tone' => 'warn', 'value' => Database::count("SELECT COUNT(*) AS c FROM bills WHERE patientId = ? AND status = 'Unpaid'", [$id])],
            ];
        }

        if ($user['role'] === 'Doctor') {
            return [
                ['label' => "Today's Appointments", 'tone' => 'info', 'value' => Database::count("SELECT COUNT(*) AS c FROM appointments WHERE doctorId = ? AND status = 'Scheduled' AND `date` = ?", [$id, $today])],
                ['label' => 'Upcoming (All)', 'tone' => 'info', 'value' => Database::count("SELECT COUNT(*) AS c FROM appointments WHERE doctorId = ? AND status = 'Scheduled' AND `date` >= ?", [$id, $today])],
                ['label' => 'Prescriptions Issued', 'tone' => 'success', 'value' => Database::count('SELECT COUNT(*) AS c FROM prescriptions WHERE doctorId = ?', [$id])],
                ['label' => 'Low Stock Medicines', 'tone' => 'danger', 'value' => Database::count('SELECT COUNT(*) AS c FROM medicines WHERE stock < ?', [$low])],
            ];
        }

        return [
            ['label' => 'Scheduled Appointments', 'tone' => 'info', 'value' => Database::count("SELECT COUNT(*) AS c FROM appointments WHERE status = 'Scheduled'")],
            ['label' => 'Unpaid Bills', 'tone' => 'warn', 'value' => Database::count("SELECT COUNT(*) AS c FROM bills WHERE status = 'Unpaid'")],
            ['label' => 'Registered Patients', 'tone' => 'success', 'value' => Database::count("SELECT COUNT(*) AS c FROM users WHERE role = 'Patient'")],
            ['label' => 'Low Stock Medicines', 'tone' => 'danger', 'value' => Database::count('SELECT COUNT(*) AS c FROM medicines WHERE stock < ?', [$low])],
        ];
    }

    // Data for the dashboard chart: appointments grouped by status (own data for Patient / Doctor, all for Receptionist)
    public static function appointmentChart(array $user): array
    {
        $where = ''; $params = [];
        if ($user['role'] === 'Patient') { $where = ' WHERE patientId = ?'; $params = [$user['id']]; }
        if ($user['role'] === 'Doctor')  { $where = ' WHERE doctorId = ?';  $params = [$user['id']]; }

        $counts = array_fill_keys(Appointment::STATUSES, 0);
        foreach (Database::all('SELECT status, COUNT(*) AS c FROM appointments' . $where . ' GROUP BY status', $params) as $r) {
            $counts[$r['status']] = (int) $r['c'];
        }
        return $counts;
    }
}
