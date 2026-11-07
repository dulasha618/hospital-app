<?php
class BillingController extends Controller
{
    private function renderForm(string $mode, array $bill, ?string $error, int $code = 200)
    {
        $this->render('billing/form', [
            'title' => $mode === 'edit' ? 'Edit Bill' : 'New Bill',
            'mode' => $mode, 'bill' => $bill, 'error' => $error,
            'statuses' => Bill::STATUSES, 'patients' => User::getByRole('Patient'),
        ], $code, 'container narrow');
    }

    private function fee($v): ?float
    {
        $f = filter_var($v, FILTER_VALIDATE_FLOAT);
        return $f === false ? null : (float) $f;
    }

    private function readBody(): array
    {
        return [
            'patientId' => parseId($_POST['patientId'] ?? null),
            'consultationFee' => $this->fee($_POST['consultationFee'] ?? ''),
            'medicineFee' => $this->fee($_POST['medicineFee'] ?? ''),
        ];
    }

    private function validate(array $d): ?string
    {
        if (!$d['patientId']) return 'Please select a patient.';
        $patient = User::findById($d['patientId']);
        if (!$patient || $patient['role'] !== 'Patient') return 'Please select a valid patient.';
        if ($d['consultationFee'] === null || $d['medicineFee'] === null) return 'Please enter valid fee amounts.';
        if ($d['consultationFee'] < 0 || $d['medicineFee'] < 0) return 'Fees cannot be negative.';
        return null;
    }

    // READ - Receptionist sees all, Patient sees own
    public function index()
    {
        $user = currentUser();
        $q = trim((string) ($_GET['q'] ?? ''));
        $filters = ['q' => $q];
        if ($user['role'] === 'Patient') $filters['patientId'] = $user['id'];

        $this->render('billing/index', ['title' => 'Billing', 'bills' => Bill::getAll($filters), 'q' => $q]);
    }

    // CREATE
    public function create()
    {
        $this->renderForm('create', ['status' => 'Unpaid'], null);
    }

    public function store()
    {
        $data = $this->readBody();
        if ($error = $this->validate($data)) return $this->renderForm('create', $data + ['status' => 'Unpaid'], $error, 400);

        Bill::create($data);
        flash('success', 'Bill created successfully.');
        $this->redirect('/billing');
    }

    // UPDATE
    public function edit(string $id)
    {
        $bill = Bill::findById(parseId($id));
        if (!$bill) return $this->notFound('/billing', 'Billing');
        $this->renderForm('edit', $bill, null);
    }

    public function update(string $id)
    {
        $bill = Bill::findById(parseId($id));
        if (!$bill) return $this->notFound('/billing', 'Billing');

        $status = $this->post('status');
        $data = $this->readBody() + ['status' => in_array($status, Bill::STATUSES, true) ? $status : $bill['status']];
        if ($error = $this->validate($data)) return $this->renderForm('edit', $data + ['id' => $bill['id']], $error, 400);

        Bill::update((int) $bill['id'], $data);
        flash('success', 'Bill updated successfully.');
        $this->redirect('/billing');
    }

    // UPDATE (payment status only)
    public function setStatus(string $id)
    {
        $status = $this->post('status');
        $bill = Bill::findById(parseId($id));
        if (!$bill) return $this->notFound('/billing', 'Billing');
        if (!in_array($status, Bill::STATUSES, true)) return $this->forbidden();

        Bill::setStatus((int) $bill['id'], $status);
        flash('success', "Bill #{$bill['id']} marked as $status.");
        $this->redirect('/billing');
    }

    // DELETE
    public function destroy(string $id)
    {
        $bill = Bill::findById(parseId($id));
        if (!$bill) return $this->notFound('/billing', 'Billing');

        Bill::remove((int) $bill['id']);
        flash('success', 'Bill deleted.');
        $this->redirect('/billing');
    }
}
