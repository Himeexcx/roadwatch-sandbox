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

$sql = "SELECT
            reports.id,
            users.full_name,
            barangays.name AS barangay_name,
            hazard_types.name AS hazard_name,
            reports.road_name,
            reports.landmark,
            reports.description,
            reports.status,
            reports.submitted_at,
            report_photos.filename
        FROM reports
        JOIN users ON reports.user_id = users.id
        JOIN barangays ON reports.barangay_id = barangays.id
        JOIN hazard_types ON reports.hazard_type_id = hazard_types.id
        LEFT JOIN report_photos ON reports.id = report_photos.report_id
        WHERE reports.status = 'pending'
        ORDER BY reports.submitted_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Reports - RoadWatch</title>
    <link rel="stylesheet" href="../includes/style.css">
    <link rel="icon" type="image/png" href="../includes/roadwatch.png">
</head>

<body>
    <main class="page-shell">
        <section class="panel">
            <h1>Pending road reports</h1>
            <nav class="nav-links">
                <a href="reports.php">Pending reports</a>
                <a href="history.php">Report history</a>
                <a href="create_user.php">Create user credentials</a>
                <a href="../auth/logout.php">Logout</a>
            </nav>

            <div class="table-wrap">
                <table border="1" cellpadding="8">
                    <tr>
                        <th>ID</th>
                        <th>Submitted By</th>
                        <th>Barangay</th>
                        <th>Hazard Type</th>
                        <th>Road Name</th>
                        <th>Landmark</th>
                        <th>Description</th>
                        <th>Photo</th>
                        <th>Submitted At</th>
                        <th>Action</th>
                    </tr>

                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['barangay_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['hazard_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['road_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['landmark']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <?php if (!empty($row['filename'])) { ?>
                                    <img
                                        src="../uploads/<?php echo htmlspecialchars($row['filename']); ?>"
                                        width="120"
                                        alt="Road hazard photo">
                                <?php } else { ?>
                                    No photo
                                <?php } ?>
                            </td>
                            <td><?php echo $row['submitted_at']; ?></td>

                            <td>
                                <form method="POST" action="verify.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <button type="submit">Verify</button>
                                </form>
                                |
                                <a href="reject.php?id=<?php echo $row['id']; ?>">
                                    <button type="button">Reject</button>
                                </a>

                            </td>

                        </tr>
                    <?php } ?>

                </table>
            </div>
        </section>

</body>

</html>