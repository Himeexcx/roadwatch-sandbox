<?php
session_start();
require '../includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
        header('Location: ../auth/login.php');
        exit;
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT reports.id, barangays.name AS barangay_name, hazard_types.name AS hazard_name, reports.road_name, reports.landmark, reports.description, reports.status, reports.passability, reports.rejection_reason, reports.submitted_at, report_photos.filename
        FROM reports JOIN barangays ON reports.barangay_id = barangays.id JOIN hazard_types ON reports.hazard_type_id = hazard_types.id LEFT JOIN report_photos ON reports.id = report_photos.report_id WHERE reports.user_id = ? ORDER BY reports.submitted_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Reports - RoadWatch</title>
        <link rel="stylesheet" href="../includes/style.css">
        <link rel="icon" type="image/png" href="../includes/roadwatch.png">
</head>

<body>
        <main class="page-shell">
                <section class="panel">
                        <h1>My road reports</h1>
                        <nav class="nav-links"><a class="button" href="submit_report.php">Submit new report</a><a class="button nav-logout" href="../auth/logout.php">Logout</a></nav>
                        <div class="table-wrap">
                                <table>
                                        <tr>
                                                <th>ID</th>
                                                <th>Barangay</th>
                                                <th>Hazard Type</th>
                                                <th>Road Name</th>
                                                <th>Landmark</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                                <th>Passability</th>
                                                <th>Rejection Reason</th>
                                                <th>Photo</th>
                                                <th>Submitted At</th>
                                        </tr>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?><tr>
                                                        <td><?php echo $row['id']; ?></td>
                                                        <td><?php echo htmlspecialchars($row['barangay_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['hazard_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['road_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['landmark']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                                                        <td><?php echo $row['passability'] !== null ? htmlspecialchars($row['passability']) : '-'; ?></td>
                                                        <td><?php echo $row['rejection_reason'] !== null ? htmlspecialchars($row['rejection_reason']) : '-'; ?></td>
                                                        <td><?php if (!empty($row['filename'])): ?><img src="../uploads/<?php echo htmlspecialchars($row['filename']); ?>" alt="Road hazard photo"><?php else: ?>No photo<?php endif; ?></td>
                                                        <td><?php echo htmlspecialchars($row['submitted_at']); ?></td>
                                                </tr><?php endwhile; ?>
                                </table>
                        </div>
                </section>
        </main>
</body>

</html>