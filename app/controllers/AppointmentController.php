<?php
class AppointmentController extends Controller
{
    // Can this user edit / delete this appointment?
    private function canManage(array $user, array $appt): bool
    {
        if ($user['role'] === 'Receptionist') return true;
        if ($user['role'] === 'Patient') return (int) $appt['patientId'] === $user['id'] && $appt['status'] === 'Scheduled';
        return false;
    }

    private function renderForm(string $mode, array $appt, ?string $error, int $code = 200)
    {
        $user = currentUser();
        $this->render('appointments/form', [
            'title' => $mode === 'edit' ? 'Edit Appointment' : 'Book Appointment',
            'mode' => $mode, 'appt' => $appt, 'error' => $error,
            'statuses' => Appointment::STATUSES,
            'doctors' => User::getByRole('Doctor'),
            'patients' => $user['role'] === 'Receptionist' ? User::getByRole('Patient') : [],
        ], $code, 'container narrow');
    }

    private function validDate(string $d): bool
    {
        $dt = DateTime::createFromFormat('Y-m-d', $d);
        return $dt && $dt->format('Y-m-d') === $d;
    }

    private function validTime(string $t): bool
    {
        return (bool) preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $t);
    }

    // Returns an error message, or null if everything is fine
    private function validate(array $d, ?array $original): ?string
    {
        if (!$d['patientId'] || !$d['doctorId'] || $d['date'] === '' || $d['time'] === '') return 'Please fill in all required fields.';
        if (!$this->validDate($d['date']) || !$this->validTime($d['time'])) return 'Please enter a valid date and time.';

        $patient = User::findById($d['patientId']);
        if (!$patient || $patient['role'] !== 'Patient') return 'Please select a valid patient.';
        $doctor = User::findById($d['doctorId']);
        if (!$doctor || $doctor['role'] !== 'Doctor') return 'Please select a valid doctor.';

        $dateChanged = !$original || substr($original['date'], 0, 10) !== $d['date'];
        if ($dateChanged && $d['date'] < today()) return 'Appointment date cannot be in the past.';

        if ($d['status'] !== 'Cancelled' && Appointment::isSlotTaken($d['doctorId'], $d['date'], $d['time'], $original ? (int) $original['id'] : 0)) {
            return 'That doctor already has an appointment at this date and time. Please choose another slot.';
        }
        return null;
    }

    // READ (list + search)
    public function index()
    {
        $user = currentUser();
        $q = trim((string) ($_GET['q'] ?? ''));
        $filters = ['q' => $q];
        if ($user['role'] === 'Patient') $filters['patientId'] = $user['id'];
        if ($user['role'] === 'Doctor') $filters['doctorId'] = $user['id'];

        $this->render('appointments/index', [
            'title' => 'Appointments', 'appointments' => Appointment::getAll($filters), 'q' => $q,
        ]);
    }

    // CREATE
    public function create()
    {
        $this->renderForm('create', ['status' => 'Scheduled'], null);
    }

    public function store()
    {
        $user = currentUser();
        $data = [
            'patientId' => $user['role'] === 'Patient' ? $user['id'] : parseId($_POST['patientId'] ?? null),
            'doctorId' => parseId($_POST['doctorId'] ?? null),
            'date' => trim($this->post('date')),
            'time' => trim($this->post('time')),
            'reason' => trim($this->post('reason')) ?: null,
            'status' => 'Scheduled',
        ];

        if ($error = $this->validate($data, null)) return $this->renderForm('create', $data, $error, 400);

        Appointment::create($data);
        flash('success', 'Appointment booked successfully.');
        $this->redirect('/appointments');
    }

    // UPDATE
    public function edit(string $id)
    {
        $user = currentUser();
        $appt = Appointment::findById(parseId($id));
        if (!$appt) return $this->notFound('/appointments', 'Appointments');
        if (!$this->canManage($user, $appt)) return $this->forbidden();
        $this->renderForm('edit', $appt, null);
    }

    public function update(string $id)
    {
        $user = currentUser();
        $appt = Appointment::findById(parseId($id));
        if (!$appt) return $this->notFound('/appointments', 'Appointments');
        if (!$this->canManage($user, $appt)) return $this->forbidden();

        $status = $this->post('status');
        $data = [
            'patientId' => $user['role'] === 'Patient' ? (int) $appt['patientId'] : parseId($_POST['patientId'] ?? null),
            'doctorId' => parseId($_POST['doctorId'] ?? null),
            'date' => trim($this->post('date')),
            'time' => trim($this->post('time')),
            'reason' => trim($this->post('reason')) ?: null,
            'status' => ($user['role'] === 'Receptionist' && in_array($status, Appointment::STATUSES, true)) ? $status : $appt['status'],
        ];

        if ($error = $this->validate($data, $appt)) return $this->renderForm('edit', $data + ['id' => $appt['id']], $error, 400);

        Appointment::update((int) $appt['id'], $data);
        flash('success', 'Appointment updated successfully.');
        $this->redirect('/appointments');
    }

    // UPDATE (status only) - Doctor (own patients) & Receptionist
    public function setStatus(string $id)
    {
        $user = currentUser();
        $status = $this->post('status');
        $appt = Appointment::findById(parseId($id));
        if (!$appt) return $this->notFound('/appointments', 'Appointments');
        if ($user['role'] === 'Doctor' && (int) $appt['doctorId'] !== $user['id']) return $this->forbidden();
        if (!in_array($status, Appointment::STATUSES, true)) return $this->forbidden();

        Appointment::setStatus((int) $appt['id'], $status);
        flash('success', "Appointment marked as $status.");
        $this->redirect('/appointments');
    }

    // DELETE
    public function destroy(string $id)
    {
        $user = currentUser();
        $appt = Appointment::findById(parseId($id));
        if (!$appt) return $this->notFound('/appointments', 'Appointments');
        if (!$this->canManage($user, $appt)) return $this->forbidden();

        Appointment::remove((int) $appt['id']);
        flash('success', $user['role'] === 'Patient' ? 'Appointment cancelled.' : 'Appointment deleted.');
        $this->redirect('/appointments');
    }
}
