<?php pageHeader([
    'heading' => $mode === 'edit' ? 'Edit Appointment' : 'Book Appointment',
    'subtitle' => $mode === 'edit' ? 'Change the doctor, date or time.' : 'Choose a doctor, date and time slot.',
    'back' => '/appointments', 'backLabel' => 'Appointments',
]); ?>

<div class="card">
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <?php if (count($doctors) === 0): ?><div class="alert alert-warn">No doctors have registered yet, so appointments cannot be booked.</div><?php endif; ?>

    <form action="<?= e(url($mode === 'edit' ? '/appointments/' . (int) $appt['id'] . '/update' : '/appointments')) ?>" method="POST">
        <?= csrfField() ?>
        <div class="form-grid">
            <?php if ($user['role'] === 'Receptionist'): ?>
                <div class="form-group full">
                    <label>Patient *</label>
                    <select name="patientId" required>
                        <option value="">-- Select patient --</option>
                        <?php foreach ($patients as $p): ?>
                            <option value="<?= (int) $p['id'] ?>" <?= (int) ($appt['patientId'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>><?= e($p['fullName']) ?> (<?= e($p['username']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (count($patients) === 0): ?><p class="hint">No patients have registered yet.</p><?php endif; ?>
                </div>
            <?php else: ?>
                <div class="form-group full">
                    <label>Patient</label>
                    <input type="text" value="<?= e($user['fullName']) ?>" readonly>
                </div>
            <?php endif; ?>

            <div class="form-group full">
                <label>Doctor *</label>
                <select name="doctorId" required>
                    <option value="">-- Select doctor --</option>
                    <?php foreach ($doctors as $d): ?>
                        <option value="<?= (int) $d['id'] ?>" <?= (int) ($appt['doctorId'] ?? 0) === (int) $d['id'] ? 'selected' : '' ?>><?= e($d['fullName']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Date *</label>
                <input type="date" name="date" value="<?= e(fmtDate($appt['date'] ?? '')) ?>" required>
            </div>
            <div class="form-group">
                <label>Time *</label>
                <input type="time" name="time" value="<?= e(fmtTime($appt['time'] ?? '')) ?>" required>
            </div>

            <div class="form-group full">
                <label>Reason for visit</label>
                <textarea name="reason" rows="3" placeholder="e.g. Chest pain, regular check-up..."><?= e($appt['reason'] ?? '') ?></textarea>
            </div>

            <?php if ($mode === 'edit' && $user['role'] === 'Receptionist'): ?>
                <div class="form-group full">
                    <label>Status</label>
                    <select name="status">
                        <?php foreach ($statuses as $s): ?>
                            <option value="<?= e($s) ?>" <?= ($appt['status'] ?? '') === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $mode === 'edit' ? 'Save Changes' : 'Confirm Booking' ?></button>
            <a href="<?= e(url('/appointments')) ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
