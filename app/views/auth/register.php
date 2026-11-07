<a href="<?= e(url('/login')) ?>" class="btn-back">← Back to Login</a>
<div class="card">
    <h2>Create Account</h2>
    <p class="subtitle">Register as a Doctor, Patient or Receptionist.</p>

    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <form action="<?= e(url('/register')) ?>" method="POST">
        <?= csrfField() ?>
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="fullName" placeholder="Enter your full name" value="<?= e($form['fullName'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Choose a username" value="<?= e($form['username'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Register as</label>
            <select name="role" required>
                <?php foreach (['Patient', 'Doctor', 'Receptionist'] as $r): ?>
                    <option value="<?= e($r) ?>" <?= ($form['role'] ?? '') === $r ? 'selected' : '' ?>><?= e($r) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="At least 6 characters" minlength="6" required>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirmPassword" placeholder="Re-enter password" minlength="6" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Register</button>
    </form>

    <p class="auth-footer">Already have an account? <a href="<?= e(url('/login')) ?>">Login here</a></p>
</div>
