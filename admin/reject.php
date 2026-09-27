<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../auth/admin_login.php");
    exit;
}

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if (!isset($_GET['id'])) {
    die("Report ID is missing.");
}

$report_id = isset($_POST["id"])
    ? (int) $_POST["id"]
    : (int) $_GET["id"];

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        die("Invalid security token.");
    }

    $admin_id = $_SESSION["user_id"];
    $status = "rejected";
    $rejection_reason = trim($_POST["rejection_reason"]);

    $sql = "UPDATE reports
            SET status = ?,
                passability = NULL,
                rejection_reason = ?,
                reviewed_by = ?,
                verified_at = NULL
            WHERE id = ? AND status = 'pending'";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssii",
        $status,
        $rejection_reason,
        $admin_id,
        $report_id
    );

    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $message = "Report rejected successfully!";
    } else {
        $message = "Report was not updated.";
    }

    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reject Report - RoadWatch</title>
    <link rel="stylesheet" href="../includes/style.css">
    <link rel="icon" type="image/png" href="../includes/roadwatch.png">
</head>

<body>
    <main class="page-shell narrow">
        <section class="panel">
            <nav class="nav-links"><a href="reports.php">Back to pending reports</a></nav>
            <h1>Reject road report</h1>
            <?php if ($message): ?><p class="notice error"><?php echo htmlspecialchars($message); ?></p><?php endif; ?>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $report_id; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <label>Reason for Rejection:</label><br><br>

                <textarea
                    name="rejection_reason"
                    rows="5"
                    cols="40"
                    required></textarea>

                <br><br>

                <button class="danger" type="submit">Reject report</button>
            </form>
        </section>
    </main>
</body>

</html>