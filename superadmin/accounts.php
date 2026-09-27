<?php
session_start();
require '../includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../auth/admin_login.php');
    exit;
}
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $security_question = trim($_POST['security_question'] ?? '');
    $security_answer = trim($_POST['security_answer'] ?? '');
    $role = $_POST['role'] ?? '';
    if (strlen($password) < 8) {
        $message = 'Password must be at least 8 characters.';
    } elseif (!in_array($role, ['user', 'admin'], true)) {
        $message = 'Select a valid account role.';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $security_answer_hash = password_hash($security_answer, PASSWORD_DEFAULT);
        $created_by = (int) $_SESSION['user_id'];
        $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, email, password_hash, security_question, security_answer, role, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssssssi', $full_name, $email, $password_hash, $security_question, $security_answer_hash, $role, $created_by);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Account created successfully.';
        } elseif (mysqli_errno($conn) === 1062) {
            $message = 'Email already exists. Please use another email.';
        } else {
            $message = 'Unable to create account.';
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
    <title>Account Management - RoadWatch</title>
    <link rel="stylesheet" href="../includes/style.css">
    <link rel="icon" type="image/png" href="../includes/roadwatch.png">
</head>

<body>
    <main class="page-shell semi-narrow">
        <section class="panel">
            <h1>Account management</h1>
            <nav class="nav-links"><a class="button" href="../superadmin/existing_accounts.php"> Existing accounts</a><a class="button" href="../superadmin/create_user.php">Create user credentials</a><a class="button nav-logout" a href="../auth/logout.php">Log out</a></nav>
            <h2>Add account</h2><?php if ($message): ?><p class="notice <?php echo str_contains($message, 'Unable') || str_contains($message, 'exists') || str_contains($message, 'valid') || str_contains($message, 'least') ? 'error' : ''; ?>"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
            <form method="POST"><label>Full name</label><input type="text" name="full_name" required><label>Email</label><input type="email" name="email" required><label>Password</label><input type="password" name="password" minlength="8" required><label>Security question</label><input type="text" name="security_question" required><label>Security answer</label><input type="text" name="security_answer" required><label>Role</label><select name="role" required>
                    <option value="">Select role</option>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select><button type="report-submit">Add account</button></form>
        </section>
    </main>
</body>

</html>