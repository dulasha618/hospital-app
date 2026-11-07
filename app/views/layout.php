<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> | MediCore Hospital System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('/public/css/style.css')) ?>">
</head>
<body>
    <div class="navbar">
        <h1>🏥 MediCore Hospital System</h1>
        <div class="nav-links">
            <?php if ($user): ?>
                <a href="<?= e(url('/dashboard')) ?>">Dashboard</a>
                <a href="<?= e(url('/profile')) ?>" class="user-chip">👤 <?= e($user['fullName']) ?> · <?= e($user['role']) ?></a>
                <form action="<?= e(url('/logout')) ?>" method="POST">
                    <?= csrfField() ?>
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            <?php else: ?>
                <a href="<?= e(url('/login')) ?>">Login</a>
                <a href="<?= e(url('/register')) ?>">Register</a>
            <?php endif; ?>
        </div>
    </div>
    <main class="<?= e($mainClass) ?>">
        <?= $content ?>
    </main>
</body>
</html>
