<?php
session_start();
require '../includes/db.php';

$message = '';
$message_type = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $security_question = trim($_POST['security_question'] ?? '');
    $security_answer = trim($_POST['security_answer'] ?? '');

    if ($full_name === '' || $email === '' || $security_question === '' || $security_answer === '') {
        $message = 'Please complete all fields.';
    } elseif (strlen($password) < 8) {
        $message = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match.';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $security_answer_hash = password_hash($security_answer, PASSWORD_DEFAULT);
        $role = 'user';

        $sql = "INSERT INTO users
                (full_name, email, password_hash, security_question, security_answer, role)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssss', $full_name, $email, $password_hash, $security_question, $security_answer_hash, $role);

        if (mysqli_stmt_execute($stmt)) {
            $message = 'Registration successful. You can now log in.';
            $message_type = 'success';
        } elseif (mysqli_errno($conn) === 1062) {
            $message = 'Email already exists. Please use another email.';
        } else {
            $message = 'Unable to create your account.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User Account - RoadWatch</title>
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
                    <div class="brand-subtitle">Community road safety</div>
                </div>
            </div>
            <h1>Create an account</h1>
            <p class="muted">Register as a user to submit and track road hazard reports.</p>
            <?php if ($message): ?><p class="notice <?php echo $message_type === 'error' ? 'error' : ''; ?>"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
            <form method="POST">
                <label for="full_name">Full name</label>
                <input id="full_name" type="text" name="full_name" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                <label for="email">Email address</label>
                <input id="email" type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" minlength="8" required>
                <label for="confirm_password">Confirm password</label>
                <input id="confirm_password" type="password" name="confirm_password" minlength="8" required>
                <label for="security_question">Security question</label>
                <input id="security_question" type="text" name="security_question" required>
                <label for="security_answer">Security answer</label>
                <input id="security_answer" type="text" name="security_answer" required>
                <button type="submit">Register!</button>
            </form>
            <p class="muted">Already have an account? <a href="login.php">Log in</a></p>
        </section>
    </main>
</body>

</html>