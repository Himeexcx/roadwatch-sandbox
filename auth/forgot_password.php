<?php
session_start();
require '../includes/db.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $stmt = mysqli_prepare($conn, 'SELECT id, email, security_question, is_active FROM users WHERE email = ?');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$user) {
        $message = 'No account found with that email.';
    } elseif (!$user['is_active']) {
        $message = 'This account is inactive.';
    } else {
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['reset_question'] = $user['security_question'];
        header('Location: security_question.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - RoadWatch</title>
    <link rel="stylesheet" href="../includes/style.css">
    <link rel="icon" type="image/png" href="../includes/roadwatch.png">
</head>

<body>
    <main class="page-shell narrow">
        <section class="panel">
            <h1>Forgot password</h1><?php if ($message): ?><p class="notice error"><?php echo htmlspecialchars($message); ?></p><?php endif; ?><form method="POST"><label for="email">Email address</label><input id="email" type="email" name="email" required><button type="submit">Continue</button></form>
            <p><a href="login.php">Back to user login</a></p>
        </section>
    </main>
</body>

</html>