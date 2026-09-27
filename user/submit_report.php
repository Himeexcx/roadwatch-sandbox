<?php
session_start();
require '../includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit;
}
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int) $_SESSION['user_id'];
    $barangay_id = (int) ($_POST['barangay_id'] ?? 0);
    $hazard_type_id = (int) ($_POST['hazard_type_id'] ?? 0);
    $road_name = trim($_POST['road_name'] ?? '');
    $landmark = trim($_POST['landmark'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $photo_valid = false;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $temporary_file = $_FILES['photo']['tmp_name'];
        $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $photo_valid = getimagesize($temporary_file) !== false && in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true) && $_FILES['photo']['size'] <= 5 * 1024 * 1024;
    }
    if (!$photo_valid) {
        $message = 'Please upload a valid JPG, JPEG, PNG, or WebP image under 5 MB.';
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO reports (user_id, barangay_id, hazard_type_id, road_name, landmark, description, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        mysqli_stmt_bind_param($stmt, 'iiisss', $user_id, $barangay_id, $hazard_type_id, $road_name, $landmark, $description);
        if (mysqli_stmt_execute($stmt)) {
            $report_id = mysqli_insert_id($conn);
            $new_filename = uniqid('road_', true) . '.' . $extension;
            if (move_uploaded_file($temporary_file, '../uploads/' . $new_filename)) {
                $photo_stmt = mysqli_prepare($conn, 'INSERT INTO report_photos (report_id, filename) VALUES (?, ?)');
                mysqli_stmt_bind_param($photo_stmt, 'is', $report_id, $new_filename);
                mysqli_stmt_execute($photo_stmt);
                mysqli_stmt_close($photo_stmt);
                $message = 'Report and photo submitted successfully.';
            } else {
                $message = 'Report saved, but the photo could not be uploaded.';
            }
        } else {
            $message = 'Failed to submit report.';
        }
        mysqli_stmt_close($stmt);
    }
}
$barangays = mysqli_query($conn, 'SELECT id, name FROM barangays ORDER BY name ASC');
$hazard_types = mysqli_query($conn, 'SELECT id, name FROM hazard_types ORDER BY name ASC');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Road Report - RoadWatch</title>
    <link rel="stylesheet" href="../includes/style.css">
    <link rel="icon" type="image/png" href="../includes/roadwatch.png">
</head>

<body>
    <main class="page-shell narrow">
        <section class="panel">
            <h1>Submit road hazard report</h1>
            <nav class="nav-links"><a href="my_reports.php">Back to my reports</a><a href="../auth/logout.php">Logout</a></nav><?php if ($message): ?><p class="notice <?php echo str_starts_with($message, 'Failed') || str_starts_with($message, 'Please') ? 'error' : ''; ?>"><?php echo htmlspecialchars($message); ?></p><?php endif; ?><form method="POST" enctype="multipart/form-data"><label>Barangay</label><select name="barangay_id" required>
                    <option value="">Select barangay</option><?php while ($barangay = mysqli_fetch_assoc($barangays)): ?><option value="<?php echo $barangay['id']; ?>"><?php echo htmlspecialchars($barangay['name']); ?></option><?php endwhile; ?>
                </select><label>Hazard type</label><select name="hazard_type_id" required>
                    <option value="">Select hazard type</option><?php while ($hazard = mysqli_fetch_assoc($hazard_types)): ?><option value="<?php echo $hazard['id']; ?>"><?php echo htmlspecialchars($hazard['name']); ?></option><?php endwhile; ?>
                </select><label>Road name</label><input type="text" name="road_name" required><label>Landmark</label><input type="text" name="landmark" required><label>Description</label><textarea name="description" rows="5" required></textarea><label>Road hazard photo</label><input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required><button type="submit">Submit report</button></form>
        </section>
    </main>
</body>

</html>