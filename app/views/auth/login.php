<div class="card">
    <h2>Welcome back 👋</h2>
    <p class="subtitle">Log in to access your portal.</p>

    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <form action="<?= e(url('/login')) ?>" method="POST">
        <?= csrfField() ?>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter username" required autofocus>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
    </form>

    <p class="auth-footer">Don't have an account? <a href="<?= e(url('/register')) ?>">Register here</a></p>
</div>
