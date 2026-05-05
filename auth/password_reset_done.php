<?php
session_start();

// prevent direct access
if (!isset($_SESSION['reset_done'])) {
    header("Location: login.php");
    exit();
}

unset($_SESSION['reset_done']);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Password Reset Successful</title>
    <link rel="stylesheet" href="../static/css/auth_forms.css">
</head>

<body>

    <div class="container">

        <img class="logo" src="../static/photos/logo.jpeg">

        <div class="title">
            <h3>Password Reset Successful</h3>
        </div>

        <div class="form">

            <p>Your password has been updated successfully.</p>

            <a href="login.php">
                <button>Go to Login</button>
            </a>

        </div>

    </div>

</body>

</html>