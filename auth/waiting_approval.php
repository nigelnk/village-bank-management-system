<?php
session_start();

// safety check
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Waiting Approval</title>
</head>

<body style="text-align:center; font-family:Arial; margin-top:100px;">

<h2>Profile Submitted</h2>

<h3>Dear <?= htmlspecialchars($username) ?>,</h3>

<p>Your account is waiting for chairperson approval.</p>
<p>Please check back later.</p>

<a href="../auth/logout.php" class="logout-btn">Logout</a>

</body>
</html>