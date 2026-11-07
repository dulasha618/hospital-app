<?php
class PharmacyController extends Controller
{
    private function renderForm(string $mode, array $medicine, ?string $error, int $code = 200)
    {
        $this->render('pharmacy/form', [
            'title' => $mode === 'edit' ? 'Edit Medicine' : 'Add Medicine',
            'mode' => $mode, 'medicine' => $medicine, 'error' => $error,
        ], $code, 'container narrow');
    }

    private function readBody(): array
    {
        $stock = filter_var($_POST['stock'] ?? '', FILTER_VALIDATE_INT);
        return ['name' => trim($this->post('name')), 'stock' => $stock === false ? null : $stock];
    }

    private function validate(array $d): ?string
    {
        if ($d['name'] === '') return 'Medicine name is required.';
        if ($d['stock'] === null || $d['stock'] < 0) return 'Stock must be a number (0 or more).';
        return null;
    }

    // READ - Doctor (view only) & Receptionist (full control)
    public function index()
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $this->render('pharmacy/index', [
            'title' => 'Pharmacy', 'medicines' => Medicine::getAll($q),
            'lowStock' => Medicine::getLowStock(), 'limit' => Medicine::limit(), 'q' => $q,
        ]);
    }

    // CREATE
    public function create()
    {
        $this->renderForm('create', ['stock' => 0], null);
    }

    public function store()
    {
        $data = $this->readBody();
        if ($error = $this->validate($data)) return $this->renderForm('create', $data, $error, 400);

        try {
            Medicine::create($data['name'], $data['stock']);
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') return $this->renderForm('create', $data, 'A medicine with that name already exists.', 400);
            throw $ex;
        }
        flash('success', 'Medicine added successfully.');
        $this->redirect('/pharmacy');
    }

    // UPDATE
    public function edit(string $id)
    {
        $medicine = Medicine::findById(parseId($id));
        if (!$medicine) return $this->notFound('/pharmacy', 'Pharmacy');
        $this->renderForm('edit', $medicine, null);
    }

    public function update(string $id)
    {
        $medicine = Medicine::findById(parseId($id));
        if (!$medicine) return $this->notFound('/pharmacy', 'Pharmacy');

        $data = $this->readBody();
        if ($error = $this->validate($data)) return $this->renderForm('edit', $data + ['id' => $medicine['id']], $error, 400);

        try {
            Medicine::update((int) $medicine['id'], $data['name'], $data['stock']);
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') return $this->renderForm('edit', $data + ['id' => $medicine['id']], 'A medicine with that name already exists.', 400);
            throw $ex;
        }
        flash('success', 'Medicine updated successfully.');
        $this->redirect('/pharmacy');
    }

    // DELETE
    public function destroy(string $id)
    {
        $medicine = Medicine::findById(parseId($id));
        if (!$medicine) return $this->notFound('/pharmacy', 'Pharmacy');

        Medicine::remove((int) $medicine['id']);
        flash('success', 'Medicine deleted.');
        $this->redirect('/pharmacy');
    }
}
