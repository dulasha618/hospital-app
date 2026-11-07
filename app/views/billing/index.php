<?php
$isRec = $user['role'] === 'Receptionist';
pageHeader([
    'heading' => $isRec ? 'Billing' : 'My Bills',
    'subtitle' => $isRec ? 'Create bills and record payments.' : 'Your bills and payment status.',
    'back' => '/dashboard', 'backLabel' => 'Dashboard',
    'action' => $isRec ? ['href' => '/billing/new', 'label' => '+ New Bill'] : null,
]);
?>
<div class="card">
    <form action="<?= e(url('/billing')) ?>" method="GET" class="search-bar">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search by patient name or status (Paid / Unpaid)...">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($q !== ''): ?><a href="<?= e(url('/billing')) ?>" class="btn btn-outline">Clear</a><?php endif; ?>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Bill</th>
                    <th>Date</th>
                    <?php if ($isRec): ?><th>Patient</th><?php endif; ?>
                    <th>Consultation</th>
                    <th>Medicine</th>
                    <th>Total</th>
                    <th>Status</th>
                    <?php if ($isRec): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (count($bills) > 0): ?>
                    <?php foreach ($bills as $b): $bid = (int) $b['id']; $paid = $b['status'] === 'Paid'; ?>
                        <tr>
                            <td>#<?= $bid ?></td>
                            <td><?= e(fmtDate($b['createdAt'])) ?></td>
                            <?php if ($isRec): ?><td><?= e($b['patientName'] ?: '(deleted user)') ?></td><?php endif; ?>
                            <td><?= e(money($b['consultationFee'])) ?></td>
                            <td><?= e(money($b['medicineFee'])) ?></td>
                            <td><b><?= e(money($b['total'])) ?></b></td>
                            <td><span class="badge <?= e(statusClass($b['status'])) ?>"><?= e($b['status']) ?></span></td>
                            <?php if ($isRec): ?>
                                <td>
                                    <div class="actions">
                                        <form action="<?= e(url("/billing/$bid/status")) ?>" method="POST" class="inline-form">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="status" value="<?= $paid ? 'Unpaid' : 'Paid' ?>">
                                            <button type="submit" class="btn <?= $paid ? 'btn-warning' : 'btn-success' ?> btn-sm"><?= $paid ? 'Mark Unpaid' : 'Mark Paid' ?></button>
                                        </form>
                                        <a href="<?= e(url("/billing/$bid/edit")) ?>" class="btn btn-outline btn-sm">Edit</a>
                                        <form action="<?= e(url("/billing/$bid/delete")) ?>" method="POST" class="inline-form">
                                            <?= csrfField() ?>
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this bill?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="<?= $isRec ? 8 : 6 ?>" class="empty">No bills found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
