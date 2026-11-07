<?php
pageHeader([
    'heading' => 'Reports',
    'subtitle' => $isRec ? 'Hospital-wide revenue, appointment and stock summary.' : 'Summary of your appointments.',
    'back' => '/dashboard', 'backLabel' => 'Dashboard',
    'action' => $isRec ? ['href' => '/reports/bills.csv', 'label' => '⬇ Export bills (CSV)'] : null,
]);
$statusColor = ['Scheduled' => 'var(--info)', 'Completed' => 'var(--success)', 'Cancelled' => 'var(--danger)'];
$statusMax = max(1, max($status));
?>

<?php if ($isRec): ?>
    <div class="stats">
        <div class="stat success"><div class="value"><?= e(money($totals['paid'])) ?></div><div class="label">Revenue collected (Paid)</div></div>
        <div class="stat warn"><div class="value"><?= e(money($totals['unpaid'])) ?></div><div class="label">Outstanding (Unpaid)</div></div>
        <div class="stat info"><div class="value"><?= e($totals['bills']) ?></div><div class="label">Total bills</div></div>
        <div class="stat danger"><div class="value"><?= count($lowStock) ?></div><div class="label">Low stock medicines</div></div>
    </div>

    <div class="card">
        <div class="chart-head">
            <h3>Monthly revenue</h3>
            <div class="range-tabs">
                <?php foreach ([3, 6, 12] as $r): ?>
                    <a href="<?= e(url('/reports?range=' . $r)) ?>" class="<?= $r === $range ? 'active' : '' ?>"><?= $r ?> months</a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php $revMax = 0; foreach ($revenue as $m) $revMax = max($revMax, $m['paid'] + $m['unpaid']); $revMax = $revMax ?: 1; ?>
        <div class="vchart">
            <?php foreach ($revenue as $m): ?>
                <div class="vcol" title="<?= e($m['label']) ?>: paid <?= e(money($m['paid'])) ?>, unpaid <?= e(money($m['unpaid'])) ?>">
                    <div class="vtotal"><?= e(money($m['paid'] + $m['unpaid'])) ?></div>
                    <div class="vbar">
                        <span class="seg unpaid" style="height: <?= round($m['unpaid'] / $revMax * 100, 1) ?>%"></span>
                        <span class="seg paid" style="height: <?= round($m['paid'] / $revMax * 100, 1) ?>%"></span>
                    </div>
                    <div class="vlabel"><?= e($m['label']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="legend"><span><i class="dot paid"></i> Paid</span><span><i class="dot unpaid"></i> Unpaid</span></div>
    </div>
<?php endif; ?>

<div class="card">
    <h3>Appointments by status</h3>
    <?php foreach ($status as $label => $count): ?>
        <div class="hrow">
            <div class="hlabel"><?= e($label) ?></div>
            <div class="htrack"><div class="hfill" style="width: <?= round($count / $statusMax * 100, 1) ?>%; background: <?= e($statusColor[$label] ?? 'var(--primary)') ?>"></div></div>
            <div class="hvalue"><?= e($count) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<?php if ($isRec): ?>
    <div class="card">
        <h3>Appointments per doctor</h3>
        <?php $docMax = 1; foreach ($perDoctor as $d) $docMax = max($docMax, (int) $d['c']); ?>
        <?php if (count($perDoctor) === 0): ?>
            <p class="empty">No doctors registered yet.</p>
        <?php endif; ?>
        <?php foreach ($perDoctor as $d): ?>
            <div class="hrow">
                <div class="hlabel"><?= e($d['label']) ?></div>
                <div class="htrack"><div class="hfill" style="width: <?= round($d['c'] / $docMax * 100, 1) ?>%"></div></div>
                <div class="hvalue"><?= e($d['c']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <h3>Low stock medicines (below <?= e(Medicine::limit()) ?>)</h3>
        <?php if (count($lowStock) === 0): ?>
            <p class="empty">All medicines are well stocked.</p>
        <?php else: ?>
            <div class="table-wrap"><table>
                <thead><tr><th>Medicine</th><th>Stock</th></tr></thead>
                <tbody>
                <?php foreach ($lowStock as $m): ?>
                    <tr><td><?= e($m['name']) ?></td><td><span class="badge badge-danger"><?= e($m['stock']) ?></span></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table></div>
        <?php endif; ?>
    </div>
<?php endif; ?>
