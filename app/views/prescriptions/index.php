<?php
$isDoctor = $user['role'] === 'Doctor';
pageHeader([
    'heading' => $isDoctor ? 'Prescriptions' : 'My Prescriptions',
    'subtitle' => $isDoctor ? 'Prescriptions you have issued.' : 'Prescriptions issued to you by your doctors.',
    'back' => '/dashboard', 'backLabel' => 'Dashboard',
    'action' => $isDoctor ? ['href' => '/prescriptions/new', 'label' => '+ New Prescription'] : null,
]);
?>
<div class="card">
    <form action="<?= e(url('/prescriptions')) ?>" method="GET" class="search-bar">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search by name, diagnosis or medicine...">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($q !== ''): ?><a href="<?= e(url('/prescriptions')) ?>" class="btn btn-outline">Clear</a><?php endif; ?>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th><?= $isDoctor ? 'Patient' : 'Doctor' ?></th>
                    <th>Diagnosis</th>
                    <th>Medicines &amp; Dosage</th>
                    <?php if ($isDoctor): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (count($prescriptions) > 0): ?>
                    <?php foreach ($prescriptions as $p): $pid = (int) $p['id']; ?>
                        <tr>
                            <td><?= e(fmtDate($p['createdAt'])) ?></td>
                            <td><?= e(($isDoctor ? $p['patientName'] : $p['doctorName']) ?: '(deleted user)') ?></td>
                            <td class="cell-wrap"><?= e($p['diagnosis']) ?></td>
                            <td class="cell-wrap"><?= e($p['medicines']) ?></td>
                            <?php if ($isDoctor): ?>
                                <td>
                                    <div class="actions">
                                        <a href="<?= e(url("/prescriptions/$pid/edit")) ?>" class="btn btn-outline btn-sm">Edit</a>
                                        <form action="<?= e(url("/prescriptions/$pid/delete")) ?>" method="POST" class="inline-form">
                                            <?= csrfField() ?>
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this prescription?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="<?= $isDoctor ? 5 : 4 ?>" class="empty">No prescriptions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
