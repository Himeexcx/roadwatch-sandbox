<?php
session_start();
require '../includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT id, full_name, email, password_hash, role, is_active
            FROM users
            WHERE email = ? AND role = 'user'
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user["password_hash"])) {

        if ($user["is_active"] == 1) {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] === "user") {
                header("Location: ../user/my_reports.php");
                exit;
            } elseif ($user["role"] === "admin") {
                header("Location: ../admin/reports.php");
                exit;
            } elseif ($user["role"] === "super_admin") {
                header("Location: ../superadmin/accounts.php");
                exit;
            }
        } else {
            $message = "This account is inactive.";
        }
    } else {
        $message = "Invalid email or password.";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RoadWatch</title>
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
            <h1>Welcome back</h1>
            <p class="muted">Sign in to submit and track road hazard reports.</p>
            <?php if ($message): ?><p class="notice error"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
            <form method="POST">
                <label for="email">Email address</label>
                <input id="email" type="email" name="email" required>
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
                <button type="submit">Log in</button>
            </form>
            <div class="actions">
                <span class="muted">New to RoadWatch?</span><a href="register.php">Create an account</a>
            </div>
            <div class="actions">
                <a href="forgot_password.php">Forgot password?</a>
                <a href="admin_login.php">Admin portal</a>
            </div>
        </section>
    </main>
</body>

</html>