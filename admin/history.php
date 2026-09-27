<?php
session_start();
require '../includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/admin_login.php');
    exit;
}
$sql = "SELECT reports.id, users.full_name, barangays.name AS barangay_name, hazard_types.name AS hazard_name,
        reports.road_name, reports.status, reports.passability, reports.rejection_reason,
        reports.submitted_at, reports.verified_at
        FROM reports JOIN users ON reports.user_id = users.id
        JOIN barangays ON reports.barangay_id = barangays.id
        JOIN hazard_types ON reports.hazard_type_id = hazard_types.id
        WHERE reports.status IN ('verified', 'rejected') ORDER BY reports.submitted_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report History - RoadWatch</title>
    <link rel="stylesheet" href="../includes/style.css">
    <link rel="icon" type="image/png" href="../includes/roadwatch.png">
</head>

<body>
    <main class="page-shell">
        <section class="panel">
            <h1>Report history</h1>
            <nav class="nav-links">
                <a class="button" a href="reports.php">Back to pending reports</a>
                <a class="button nav-logout" href="../auth/logout.php">Log out</a>
            </nav>
            <div class="table-wrap">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Submitted By</th>
                        <th>Barangay</th>
                        <th>Hazard Type</th>
                        <th>Road Name</th>
                        <th>Status</th>
                        <th>Passability</th>
                        <th>Rejection Reason</th>
                        <th>Submitted At</th>
                        <th>Verified At</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?><tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['barangay_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['hazard_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['road_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo $row['passability'] !== null ? htmlspecialchars($row['passability']) : '-'; ?></td>
                            <td><?php echo $row['rejection_reason'] !== null ? htmlspecialchars($row['rejection_reason']) : '-'; ?></td>
                            <td><?php echo htmlspecialchars($row['submitted_at']); ?></td>
                            <td><?php echo $row['verified_at'] !== null ? htmlspecialchars($row['verified_at']) : '-'; ?></td>
                        </tr><?php endwhile; ?>
                </table>
            </div>
        </section>
    </main>
</body>

</html>