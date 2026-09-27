<?php
session_start();
require '../includes/db.php';

// Only Super Admin can access this
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "super_admin") {
    header("Location: ../auth/admin_login.php");
    exit;
}

if (isset($_GET["id"])) {
    $account_id = (int) $_GET["id"];

    // Prevent Super Admin from deactivating their own account
    if ($account_id === (int) $_SESSION["user_id"]) {
        header("Location: accounts.php");
        exit;
    }

    $sql = "UPDATE users
            SET is_active = IF(is_active = 1, 0, 1)
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $account_id);
    mysqli_stmt_execute($stmt);
}

header("Location: accounts.php");
exit;
?>