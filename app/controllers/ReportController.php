<?php
class ReportController extends Controller
{
    // Doctor: own appointment summary | Receptionist: hospital-wide report
    public function index()
    {
        $user = currentUser();
        $isRec = $user['role'] === 'Receptionist';
        $range = (int) ($_GET['range'] ?? 6);
        if (!in_array($range, [3, 6, 12], true)) $range = 6;

        $data = [
            'title'   => 'Reports',
            'isRec'   => $isRec,
            'range'   => $range,
            'status'  => Report::appointmentsByStatus($isRec ? null : (int) $user['id']),
        ];
        if ($isRec) {
            $data['revenue']  = Report::monthlyRevenue($range);
            $data['perDoctor'] = Report::appointmentsPerDoctor();
            $data['totals']   = Report::billTotals();
            $data['lowStock'] = Medicine::getLowStock();
        }
        $this->render('reports/index', $data);
    }

    // Download every bill as a CSV file (opens in Excel)
    public function exportBills()
    {
        $bills = Bill::getAll();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="bills-' . today() . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Bill', 'Date', 'Patient', 'Consultation', 'Medicine', 'Total', 'Status']);
        foreach ($bills as $b) {
            fputcsv($out, [
                $b['id'], fmtDate($b['createdAt']), $this->safeCell((string) $b['patientName']),
                $b['consultationFee'], $b['medicineFee'], $b['total'], $b['status'],
            ]);
        }
        fclose($out);
        exit;
    }

    // Stop spreadsheet formula injection (a name like "=cmd|..." would run in Excel)
    private function safeCell(string $v): string
    {
        return ($v !== '' && strpos('=+-@', $v[0]) !== false) ? "'" . $v : $v;
    }
}
