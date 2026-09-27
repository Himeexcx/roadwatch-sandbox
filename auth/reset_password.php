<?php
session_start();
require '../includes/db.php';
if (!isset($_SESSION['reset_user_id'], $_SESSION['reset_verified']) || $_SESSION['reset_verified'] !== true) {
    header('Location: forgot_password.php');
    exit;
}
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    if (strlen($new_password) < 8) {
        $message = 'Password must be at least 8 characters.';
    } elseif ($new_password !== $confirm_password) {
        $message = 'Passwords do not match.';
    } else {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $user_id = (int) $_SESSION['reset_user_id'];
        $stmt = mysqli_prepare($conn, 'UPDATE users SET password_hash = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'si', $password_hash, $user_id);
        if (mysqli_stmt_execute($stmt)) {
            unset($_SESSION['reset_user_id'], $_SESSION['reset_question'], $_SESSION['reset_verified']);
            $message = 'Password reset successful. You can now log in.';
        } else {
            $message = 'Password reset failed.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - RoadWatch</title>
    <link rel="stylesheet" href="../includes/style.css">
    <link rel="icon" type="image/png" href="../includes/roadwatch.png">
</head>

<body>
    <main class="page-shell narrow">
        <section class="panel">
            <h1>Reset password</h1><?php if ($message): ?><p class="notice <?php echo str_contains($message, 'successful') ? '' : 'error'; ?>"><?php echo htmlspecialchars($message); ?></p><?php endif; ?><form method="POST"><label for="new_password">New password</label><input id="new_password" type="password" name="new_password" minlength="8" required><label for="confirm_password">Confirm new password</label><input id="confirm_password" type="password" name="confirm_password" minlength="8" required><button type="submit">Reset password</button></form>
            <p><a href="login.php">Back to user login</a></p>
        </section>
    </main>
</body>

</html>