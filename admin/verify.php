<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../auth/admin_login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["id"])) {
    die("Report ID is missing.");
}

if (
    !isset($_POST["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    die("Invalid security token.");
}

$report_id = (int) $_POST["id"];
$admin_id = $_SESSION["user_id"];

$status = "verified";
$passability = "passable";

$sql = "UPDATE reports
        SET status = ?,
            passability = ?,
            reviewed_by = ?,
            verified_at = NOW(),
            rejection_reason = NULL
        WHERE id = ? AND status = 'pending'";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ssii",
    $status,
    $passability,
    $admin_id,
    $report_id
);

mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo "Report verified successfully!";
} else {
    echo "Report was not updated.";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
