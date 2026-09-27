<?php
session_start();
require '../includes/db.php';
if (!isset($_SESSION['reset_user_id'], $_SESSION['reset_question'])) {
    header('Location: forgot_password.php');
    exit;
}
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answer = trim($_POST['security_answer'] ?? '');
    $user_id = (int) $_SESSION['reset_user_id'];
    $stmt = mysqli_prepare($conn, 'SELECT security_answer FROM users WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    $stored_answer = $user['security_answer'] ?? '';
    if ($user && (password_verify($answer, $stored_answer) || strcasecmp(trim($stored_answer), $answer) === 0)) {
        $_SESSION['reset_verified'] = true;
        header('Location: reset_password.php');
        exit;
    }
    $message = 'Incorrect security answer.';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Question - RoadWatch</title>
    <link rel="stylesheet" href="../includes/style.css">
    <link rel="icon" type="image/png" href="../includes/roadwatch.png">
</head>

<body>
    <main class="page-shell narrow">
        <section class="panel">
            <h1>Security question</h1>
            <p><?php echo htmlspecialchars($_SESSION['reset_question']); ?></p><?php if ($message): ?><p class="notice error"><?php echo htmlspecialchars($message); ?></p><?php endif; ?><form method="POST"><label for="security_answer">Your answer</label><input id="security_answer" type="text" name="security_answer" required><button type="submit">Verify answer</button></form>
            <p><a href="forgot_password.php">Back</a></p>
        </section>
    </main>
</body>

</html>