<?php
class PrescriptionController extends Controller
{
    private function renderForm(string $mode, array $rx, ?string $error, int $code = 200)
    {
        $this->render('prescriptions/form', [
            'title' => $mode === 'edit' ? 'Edit Prescription' : 'New Prescription',
            'mode' => $mode, 'rx' => $rx, 'error' => $error, 'patients' => User::getByRole('Patient'),
        ], $code, 'container narrow');
    }

    private function validate(array $d): ?string
    {
        if (!$d['patientId'] || $d['diagnosis'] === '' || $d['medicines'] === '') return 'Please fill in all required fields.';
        $patient = User::findById($d['patientId']);
        if (!$patient || $patient['role'] !== 'Patient') return 'Please select a valid patient.';
        return null;
    }

    // READ - Doctor sees what he wrote, Patient sees what he received
    public function index()
    {
        $user = currentUser();
        $q = trim((string) ($_GET['q'] ?? ''));
        $filters = ['q' => $q];
        if ($user['role'] === 'Doctor') $filters['doctorId'] = $user['id'];
        if ($user['role'] === 'Patient') $filters['patientId'] = $user['id'];

        $this->render('prescriptions/index', [
            'title' => 'Prescriptions', 'prescriptions' => Prescription::getAll($filters), 'q' => $q,
        ]);
    }

    // CREATE
    public function create()
    {
        $this->renderForm('create', [], null);
    }

    public function store()
    {
        $data = [
            'patientId' => parseId($_POST['patientId'] ?? null),
            'doctorId' => currentUser()['id'],
            'diagnosis' => trim($this->post('diagnosis')),
            'medicines' => trim($this->post('medicines')),
        ];
        if ($error = $this->validate($data)) return $this->renderForm('create', $data, $error, 400);

        Prescription::create($data);
        flash('success', 'Prescription saved successfully.');
        $this->redirect('/prescriptions');
    }

    // UPDATE
    public function edit(string $id)
    {
        $rx = Prescription::findById(parseId($id));
        if (!$rx) return $this->notFound('/prescriptions', 'Prescriptions');
        if ((int) $rx['doctorId'] !== currentUser()['id']) return $this->forbidden();
        $this->renderForm('edit', $rx, null);
    }

    public function update(string $id)
    {
        $rx = Prescription::findById(parseId($id));
        if (!$rx) return $this->notFound('/prescriptions', 'Prescriptions');
        if ((int) $rx['doctorId'] !== currentUser()['id']) return $this->forbidden();

        $data = [
            'patientId' => parseId($_POST['patientId'] ?? null),
            'diagnosis' => trim($this->post('diagnosis')),
            'medicines' => trim($this->post('medicines')),
        ];
        if ($error = $this->validate($data)) return $this->renderForm('edit', $data + ['id' => $rx['id']], $error, 400);

        Prescription::update((int) $rx['id'], $data);
        flash('success', 'Prescription updated successfully.');
        $this->redirect('/prescriptions');
    }

    // DELETE
    public function destroy(string $id)
    {
        $rx = Prescription::findById(parseId($id));
        if (!$rx) return $this->notFound('/prescriptions', 'Prescriptions');
        if ((int) $rx['doctorId'] !== currentUser()['id']) return $this->forbidden();

        Prescription::remove((int) $rx['id']);
        flash('success', 'Prescription deleted.');
        $this->redirect('/prescriptions');
    }
}
