<?php
session_start();
require_once '../utils/config.php';

$conn = get_db();

if (!isset($_SESSION['reset_user'])) {
    header("Location: forgot_password.php");
    exit();
}

$user_id = $_SESSION['reset_user'];

if (isset($_POST['password'])) {
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $conn->query("UPDATE users SET password_hash='$hash' WHERE id=$user_id");

    // after successful update
    $_SESSION['reset_done'] = true;

    header("Location: password_reset_done.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="../static/css/auth_forms.css">
</head>

<body>

    <div class="container">

        <img class="logo" src="../static/photos/logo.jpeg">

        <div class="title">
            <h3>Reset Password</h3>
        </div>

        <div class="form">

            <form method="POST">
                <input type="password" name="password" placeholder="New Password" required>
                <button>Reset Password</button>
            </form>

        </div>

    </div>

</body>

</html>