<?php
require 'includes/db.php';

$sql = "SELECT * FROM barangays ORDER BY name ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoadWatch Dagupan</title>
    <link rel="stylesheet" href="includes/style.css">
    <link rel="icon" type="image/png" href="includes/roadwatch.png">
</head>
<body>
<header class="site-header">
    <div class="brand-lockup">
        <img src="includes/roadwatch.png" alt="RoadWatch logo" class="brand-logo">
        <div>
            <h1>RoadWatch Dagupan</h1>
            <p>Community-powered road hazard reporting.</p>
        </div>
    </div>
</header>
<main class="page-shell">
    <section class="panel">
        <div class="actions">
            <a class="button" href="auth/login.php">User login</a>
            <a class="button" href="auth/admin_login.php">Admin portal</a>
            <a class="button secondary" href="auth/register.php">Register as a user</a>
        </div>
        <div class="feature-grid">
            <div class="feature"><strong>Report hazards</strong><br><span class="muted">Send road condition reports with photos.</span></div>
            <div class="feature"><strong>Track progress</strong><br><span class="muted">See the status of your submitted reports.</span></div>
            <div class="feature"><strong>Browse coverage</strong><br><span class="muted">RoadWatch currently covers these barangays:</span></div>
        </div>
        <div class="coverage-list" aria-label="Covered barangays">
            <?php
            $currentLetter = '';
            while ($row = mysqli_fetch_assoc($result)):
                $letter = strtoupper(substr(trim($row['name']), 0, 1));
                if ($letter !== $currentLetter):
                    if ($currentLetter !== ''): ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="coverage-group">
                        <h3><?php echo htmlspecialchars($letter); ?></h3>
                        <div class="coverage-names">
                    <?php $currentLetter = $letter; ?>
                <?php endif; ?>
                <span><?php echo htmlspecialchars($row['name']); ?></span>
            <?php endwhile; ?>
            <?php if ($currentLetter !== ''): ?>
                        </div>
                    </div>
            <?php endif; ?>
        </div>
    </section>
</main>
</body>
</html>