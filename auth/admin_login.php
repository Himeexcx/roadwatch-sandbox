<?php
session_start();
require '../includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $sql = "SELECT id, full_name, password_hash, role, is_active
            FROM users WHERE email = ? AND role IN ('admin', 'super_admin') LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($user && password_verify($password, $user['password_hash'])) {
        if ((int) $user['is_active'] === 1) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            header('Location: ' . ($user['role'] === 'super_admin' ? '../superadmin/accounts.php' : '../admin/reports.php'));
            exit;
        }
        $message = 'This account is inactive.';
    } else {
        $message = 'Invalid administrator credentials.';
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - RoadWatch</title>
    <link rel="stylesheet" href="../includes/style.css">
    <link rel="icon" type="image/png" href="../includes/roadwatch.png">
</head>

<body>
    <main class="auth-page">
        <section class="auth-card">
            <a class="back-link" href="../index.php">&larr; Back to RoadWatch</a>
            <div class="brand-lockup">
                <span class="brand-mark">RW</span>
                <div>
                    <div class="brand-name">ROADWATCH</div>
                    <div class="brand-subtitle">Administration portal</div>
                </div>
            </div>
            <h1>Admin portal</h1>
            <p class="muted">For administrators and super administrators only.</p>
            <?php if ($message): ?><p class="notice error"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
            <form method="POST">
                <label for="email">Admin email</label>
                <input id="email" type="email" name="email" required>
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
                <button type="submit">Log in</button>
            </form>
            <p class="muted">Need a citizen account? <a href="login.php">User login</a></p>
        </section>
    </main>
</body>

</html>