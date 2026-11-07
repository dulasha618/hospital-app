<div class="card error-box">
    <h2><?= e($heading ?? 'Error') ?></h2>
    <p><?= e($message ?? '') ?></p>
    <a href="<?= e(url($back ?? '/')) ?>" class="btn btn-primary">← Back to <?= e($backLabel ?? 'Home') ?></a>
</div>
