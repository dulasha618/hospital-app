<?php
$isRec = $user['role'] === 'Receptionist';
pageHeader([
    'heading' => 'Pharmacy Stock & Inventory',
    'subtitle' => $isRec ? 'Add, update and remove medicines.' : 'Check medicine availability (view only).',
    'back' => '/dashboard', 'backLabel' => 'Dashboard',
    'action' => $isRec ? ['href' => '/pharmacy/new', 'label' => '+ Add Medicine'] : null,
]);
?>
<?php if (count($lowStock) > 0): ?>
    <div class="alert alert-error">
        <b>⚠️ Low Stock Alert</b> (below <?= (int) $limit ?> units):
        <?php foreach ($lowStock as $i => $item): ?><strong><?= e($item['name']) ?></strong> (<?= (int) $item['stock'] ?> left)<?= $i < count($lowStock) - 1 ? ', ' : '' ?><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card">
    <form action="<?= e(url('/pharmacy')) ?>" method="GET" class="search-bar">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search medicine name...">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($q !== ''): ?><a href="<?= e(url('/pharmacy')) ?>" class="btn btn-outline">Clear</a><?php endif; ?>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Available Stock</th>
                    <th>Status</th>
                    <?php if ($isRec): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (count($medicines) > 0): ?>
                    <?php foreach ($medicines as $m): $mid = (int) $m['id']; ?>
                        <tr>
                            <td><?= e($m['name']) ?></td>
                            <td><?= (int) $m['stock'] ?></td>
                            <td>
                                <?php if ((int) $m['stock'] < $limit): ?>
                                    <span class="badge badge-danger">Low Stock</span>
                                <?php else: ?>
                                    <span class="badge badge-success">In Stock</span>
                                <?php endif; ?>
                            </td>
                            <?php if ($isRec): ?>
                                <td>
                                    <div class="actions">
                                        <a href="<?= e(url("/pharmacy/$mid/edit")) ?>" class="btn btn-outline btn-sm">Edit</a>
                                        <form action="<?= e(url("/pharmacy/$mid/delete")) ?>" method="POST" class="inline-form">
                                            <?= csrfField() ?>
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this medicine?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="<?= $isRec ? 4 : 3 ?>" class="empty">No medicines found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
