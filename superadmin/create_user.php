<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'super_admin'], true)) {
    header('Location: ../auth/admin_login.php');
    exit;
}

$message = '';
$message_type = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $security_question = trim($_POST['security_question'] ?? '');
    $security_answer = trim($_POST['security_answer'] ?? '');

    if ($full_name === '' || $email === '' || $security_question === '' || $security_answer === '') {
        $message = 'Please complete all fields.';
    } elseif (strlen($password) < 8) {
        $message = 'Password must be at least 8 characters.';
    } else {
        $role = 'user';
        $created_by = (int) $_SESSION['user_id'];
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $security_answer_hash = password_hash($security_answer, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users
                (full_name, email, password_hash, security_question, security_answer, role, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssssi', $full_name, $email, $password_hash, $security_question, $security_answer_hash, $role, $created_by);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'User credentials created successfully.';
            $message_type = 'success';
        } elseif (mysqli_errno($conn) === 1062) {
            $message = 'Email already exists. Please use another email.';
        } else {
            $message = 'Unable to create the user account.';
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
    <title>Create User Credentials - RoadWatch</title>
    <link rel="stylesheet" href="../includes/style.css">
    <link rel="icon" type="image/png" href="../includes/roadwatch.png">
</head>

<body>
    <main class="page-shell narrow">
        <section class="panel">
            <nav class="nav-links">
                <a class="button" href="../superadmin/accounts.php">Go Back</a>
                <a class="button nav-logout" href="../auth/logout.php">Log out</a></nav>
            <h1>Create user credentials</h1>
            <p class="muted">Manual registration creates a standard user account that can submit reports.</p>
            <?php if ($message): ?><p class="notice <?php echo $message_type === 'error' ? 'error' : ''; ?>"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
            <form method="POST">
                <label for="full_name">Full name</label><input id="full_name" type="text" name="full_name" required>
                <label for="email">Email address</label><input id="email" type="email" name="email" required>
                <label for="password">Temporary password</label><input id="password" type="password" name="password" minlength="8" required>
                <label for="security_question">Security question</label><input id="security_question" type="text" name="security_question" required>
                <label for="security_answer">Security answer</label><input id="security_answer" type="text" name="security_answer" required>
                <button type="account-submit">Create user account</button>
            </form>
        </section>
    </main>
</body>

</html>