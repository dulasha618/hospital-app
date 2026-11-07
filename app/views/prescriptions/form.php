<?php pageHeader([
    'heading' => $mode === 'edit' ? 'Edit Prescription' : 'New Prescription',
    'subtitle' => 'Write the diagnosis and the medicines for the patient.',
    'back' => '/prescriptions', 'backLabel' => 'Prescriptions',
]); ?>

<div class="card">
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <form action="<?= e(url($mode === 'edit' ? '/prescriptions/' . (int) $rx['id'] . '/update' : '/prescriptions')) ?>" method="POST">
        <?= csrfField() ?>
        <div class="form-grid">
            <div class="form-group full">
                <label>Patient *</label>
                <select name="patientId" required>
                    <option value="">-- Select patient --</option>
                    <?php foreach ($patients as $p): ?>
                        <option value="<?= (int) $p['id'] ?>" <?= (int) ($rx['patientId'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>><?= e($p['fullName']) ?> (<?= e($p['username']) ?>)</option>
                    <?php endforeach; ?>
                </select>
                <?php if (count($patients) === 0): ?><p class="hint">No patients have registered yet.</p><?php endif; ?>
            </div>
            <div class="form-group full">
                <label>Medical Diagnosis *</label>
                <textarea name="diagnosis" rows="3" placeholder="Enter diagnosis details..." required><?= e($rx['diagnosis'] ?? '') ?></textarea>
            </div>
            <div class="form-group full">
                <label>Medicines &amp; Dosage Instructions *</label>
                <textarea name="medicines" rows="3" placeholder="e.g. Paracetamol 500mg (1-0-1) for 5 days" required><?= e($rx['medicines'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $mode === 'edit' ? 'Save Changes' : 'Save &amp; Issue Prescription' ?></button>
            <a href="<?= e(url('/prescriptions')) ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
