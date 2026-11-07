<?php pageHeader([
    'heading' => $mode === 'edit' ? 'Edit Bill' : 'New Bill',
    'subtitle' => 'The total is calculated automatically.',
    'back' => '/billing', 'backLabel' => 'Billing',
]); ?>

<div class="card">
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <form action="<?= e(url($mode === 'edit' ? '/billing/' . (int) $bill['id'] . '/update' : '/billing')) ?>" method="POST">
        <?= csrfField() ?>
        <div class="form-grid">
            <div class="form-group full">
                <label>Patient *</label>
                <select name="patientId" required>
                    <option value="">-- Select patient --</option>
                    <?php foreach ($patients as $p): ?>
                        <option value="<?= (int) $p['id'] ?>" <?= (int) ($bill['patientId'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>><?= e($p['fullName']) ?> (<?= e($p['username']) ?>)</option>
                    <?php endforeach; ?>
                </select>
                <?php if (count($patients) === 0): ?><p class="hint">No patients have registered yet.</p><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Consultation Fee ($) *</label>
                <input type="number" name="consultationFee" min="0" step="0.01" placeholder="0.00" value="<?= e($bill['consultationFee'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Pharmacy &amp; Medicine Fee ($) *</label>
                <input type="number" name="medicineFee" min="0" step="0.01" placeholder="0.00" value="<?= e($bill['medicineFee'] ?? '') ?>" required>
            </div>
            <?php if ($mode === 'edit'): ?>
                <div class="form-group full">
                    <label>Payment Status</label>
                    <select name="status">
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?= e($s) ?>" <?= ($bill['status'] ?? '') === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $mode === 'edit' ? 'Save Changes' : 'Create Bill' ?></button>
            <a href="<?= e(url('/billing')) ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
