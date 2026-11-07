<div class="page-header">
    <div>
        <h2>Welcome, <?= e($user['fullName']) ?> 👋</h2>
        <p class="subtitle">You are logged in as <b><?= e($user['role']) ?></b>.</p>
    </div>
</div>
<?php flashAlert(); ?>

<div class="stats">
    <?php foreach ($stats as $s): ?>
        <div class="stat <?= e($s['tone']) ?>">
            <div class="value"><?= e($s['value']) ?></div>
            <div class="label"><?= e($s['label']) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<?php
$colors = ['Scheduled' => 'var(--info)', 'Completed' => 'var(--success)', 'Cancelled' => 'var(--danger)'];
$chartMax = max(1, max($chart));
?>
<div class="card chart-card">
    <h3>📊 Appointments by status</h3>
    <?php foreach ($chart as $label => $count): ?>
        <div class="hrow">
            <div class="hlabel"><?= e($label) ?></div>
            <div class="htrack"><div class="hfill" style="width: <?= round($count / $chartMax * 100, 1) ?>%; background: <?= e($colors[$label] ?? 'var(--primary)') ?>"></div></div>
            <div class="hvalue"><?= e($count) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<div class="modules">
    <?php foreach ($modules as [$icon, $mTitle, $text, $link, $button]): ?>
        <div class="module">
            <div class="icon"><?= e($icon) ?></div>
            <h3><?= e($mTitle) ?></h3>
            <p><?= e($text) ?></p>
            <a href="<?= e(url($link)) ?>" class="btn btn-primary btn-sm"><?= e($button) ?></a>
        </div>
    <?php endforeach; ?>
</div>
