<?php
session_start();

// safety check
if (!isset($_SESSION['username'])) {
    $_SESSION["error_message"] = "No username";
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!-- <!DOCTYPE html>
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
</html> -->

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Village Bank - Waiting For Approval</title>

    <link rel="stylesheet" href="../static/css/signin.css">
    <link rel="stylesheet" href="../static/css/waiting_approval.css">

</head>

<body>

    <div class="container">

        <div class="subtitle">
            <img class="logo" src="../static/photos/logo.jpeg" alt="Village Bank Logo">
        </div>
        <div class="title">
            <h3>PROFILE SUBMITTED</h3>
        </div>

        <div class="form">
            <h4>Waiting For Approval</h4>
            <h5> Dear <?php echo $username;?>,</h5>
            <p>You account is waiting for Chairperson approval. Please check back later.</p>
            <a href="logout.php">Return to login</a>
        </div>

    </div>

</body>

</html>
