<?php
session_start();
require '../includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: ../auth/admin_login.php');
    exit;
}

$accounts_result = mysqli_query(
    $conn,
    'SELECT id, full_name, email, role, is_active, created_at FROM users ORDER BY created_at DESC'
);
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
    <main class="page-shell">
        <section class="panel">
            <h1>Account management</h1>
            <nav class="nav-links">
                <a class="button" href="../superadmin/accounts.php">Go back</a>
                <a class="button nav-logout" href="../auth/logout.php">Log out</a>
            </nav>
            <h2>Existing accounts</h2>
            <div class="table-wrap">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($account = mysqli_fetch_assoc($accounts_result)): ?><tr>
                            <td><?php echo $account['id']; ?></td>
                            <td><?php echo htmlspecialchars($account['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($account['email']); ?></td>
                            <td><?php echo htmlspecialchars($account['role']); ?></td>
                            <td><?php echo $account['is_active'] ? 'Active' : 'Inactive'; ?></td>
                            <td><?php echo htmlspecialchars($account['created_at']); ?></td>
                            <td><?php if ((int) $account['id'] !== (int) $_SESSION['user_id']): ?><a class="button <?php echo $account['is_active'] ? 'danger' : ''; ?>" href="toggle_account.php?id=<?php echo $account['id']; ?>"><?php echo $account['is_active'] ? 'Deactivate' : 'Activate'; ?></a><?php else: ?>-<?php endif; ?></td>
                        </tr><?php endwhile; ?>
                </table>
            </div>
        </section>
    </main>
</body>

</html>