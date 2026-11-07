<?php pageHeader([
    'heading' => $mode === 'edit' ? 'Edit Medicine' : 'Add Medicine',
    'subtitle' => 'Stock below ' . Medicine::limit() . ' units shows a low stock alert.',
    'back' => '/pharmacy', 'backLabel' => 'Pharmacy',
]); ?>

<div class="card">
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <form action="<?= e(url($mode === 'edit' ? '/pharmacy/' . (int) $medicine['id'] . '/update' : '/pharmacy')) ?>" method="POST">
        <?= csrfField() ?>
        <div class="form-grid">
            <div class="form-group full">
                <label>Medicine Name *</label>
                <input type="text" name="name" placeholder="e.g. Paracetamol 500mg" value="<?= e($medicine['name'] ?? '') ?>" required>
            </div>
            <div class="form-group full">
                <label>Stock (units) *</label>
                <input type="number" name="stock" min="0" step="1" value="<?= e($medicine['stock'] ?? 0) ?>" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $mode === 'edit' ? 'Save Changes' : 'Add Medicine' ?></button>
            <a href="<?= e(url('/pharmacy')) ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
