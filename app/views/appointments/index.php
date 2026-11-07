<?php
$role = $user['role'];
pageHeader([
    'heading' => $role === 'Receptionist' ? 'Appointments Management' : 'My Appointments',
    'subtitle' => $role === 'Patient' ? 'Your booked appointments.' : ($role === 'Doctor' ? 'Appointments booked with you.' : 'All hospital appointments.'),
    'back' => '/dashboard', 'backLabel' => 'Dashboard',
    'action' => $role === 'Doctor' ? null : ['href' => '/appointments/new', 'label' => '+ Book Appointment'],
]);
?>
<div class="card">
    <form action="<?= e(url('/appointments')) ?>" method="GET" class="search-bar">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search by patient, doctor, reason or status...">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($q !== ''): ?><a href="<?= e(url('/appointments')) ?>" class="btn btn-outline">Clear</a><?php endif; ?>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <?php if ($role !== 'Patient'): ?><th>Patient</th><?php endif; ?>
                    <?php if ($role !== 'Doctor'): ?><th>Doctor</th><?php endif; ?>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($appointments) > 0): ?>
                    <?php foreach ($appointments as $a): $aid = (int) $a['id']; ?>
                        <tr>
                            <?php if ($role !== 'Patient'): ?><td><?= e($a['patientName'] ?: '(deleted user)') ?></td><?php endif; ?>
                            <?php if ($role !== 'Doctor'): ?><td><?= e($a['doctorName'] ?: '(deleted user)') ?></td><?php endif; ?>
                            <td><?= e(fmtDate($a['date'])) ?></td>
                            <td><?= e(fmtTime($a['time'])) ?></td>
                            <td class="cell-wrap"><?= e($a['reason'] ?: '-') ?></td>
                            <td><span class="badge <?= e(statusClass($a['status'])) ?>"><?= e($a['status']) ?></span></td>
                            <td>
                                <div class="actions">
                                    <?php if ($role === 'Doctor' || $role === 'Receptionist'): ?>
                                        <?php if ($a['status'] === 'Scheduled'): ?>
                                            <form action="<?= e(url("/appointments/$aid/status")) ?>" method="POST" class="inline-form">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="status" value="Completed">
                                                <button type="submit" class="btn btn-success btn-sm">Complete</button>
                                            </form>
                                            <form action="<?= e(url("/appointments/$aid/status")) ?>" method="POST" class="inline-form">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="status" value="Cancelled">
                                                <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Mark this appointment as cancelled?')">Cancel</button>
                                            </form>
                                        <?php else: ?>
                                            <form action="<?= e(url("/appointments/$aid/status")) ?>" method="POST" class="inline-form">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="status" value="Scheduled">
                                                <button type="submit" class="btn btn-outline btn-sm">Reopen</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($role === 'Receptionist'): ?>
                                        <a href="<?= e(url("/appointments/$aid/edit")) ?>" class="btn btn-outline btn-sm">Edit</a>
                                        <form action="<?= e(url("/appointments/$aid/delete")) ?>" method="POST" class="inline-form">
                                            <?= csrfField() ?>
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this appointment permanently?')">Delete</button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($role === 'Patient'): ?>
                                        <?php if ($a['status'] === 'Scheduled'): ?>
                                            <a href="<?= e(url("/appointments/$aid/edit")) ?>" class="btn btn-outline btn-sm">Edit</a>
                                            <form action="<?= e(url("/appointments/$aid/delete")) ?>" method="POST" class="inline-form">
                                                <?= csrfField() ?>
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this appointment?')">Cancel</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="hint">-</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="<?= $role === 'Receptionist' ? 7 : 6 ?>" class="empty">No appointments found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
