<?php pageHeader(['heading' => 'My Profile', 'subtitle' => 'Update your name or change your password.', 'back' => '/dashboard', 'backLabel' => 'Dashboard']); ?>
<div class="card">
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form action="<?= e(url('/profile')) ?>" method="POST">
        <?= csrfField() ?>
        <div class="form-grid">
            <div class="form-group full">
                <label>Full Name</label>
                <input type="text" name="fullName" value="<?= e($account['fullName']) ?>" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" value="<?= e($account['username']) ?>" readonly>
            </div>
            <div class="form-group">
                <label>Role</label>
                <input type="text" value="<?= e($account['role']) ?>" readonly>
            </div>
        </div>

        <div class="form-section">
            <h3>Change Password <span class="hint">(leave blank to keep the current one)</span></h3>
            <div class="form-grid">
                <div class="form-group full">
                    <label>Current Password</label>
                    <input type="password" name="currentPassword" autocomplete="current-password">
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="newPassword" minlength="6" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirmPassword" minlength="6" autocomplete="new-password">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?= e(url('/dashboard')) ?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
