<?php
session_start();

$logout_portal = ($_SESSION['role'] ?? 'user') === 'user'
	? 'login.php'
	: 'admin_login.php';

session_unset();
session_destroy();

header("Location: " . $logout_portal);
exit;
