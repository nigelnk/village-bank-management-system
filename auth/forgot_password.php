<?php
session_start();
require_once '../utils/config.php';

$conn = get_db();
$error = "";

if (isset($_POST['username'])) {
    $username = $_POST['username'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    $user = $result->fetch_assoc();

    if ($user && $user['security_question']) {
        $_SESSION['reset_user'] = $user['id'];
        header("Location: verify_security.php");
        exit();
    } else {
        $error = "User not found or no security question set.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../static/css/auth_forms.css">
</head>

<body>

    <div class="container">

        <img class="logo" src="../static/photos/logo.jpeg">

        <div class="title">
            <h3>Forgot Password</h3>
        </div>

        <div class="form">

            <form method="POST">
                <input type="text" name="username" placeholder="Enter Username" required>
                <button>Next</button>
            </form>

            <p class="error"><?= $error ?></p>

            <a class="link" href="login.php">Back to Login</a>
        </div>

    </div>

</body>

</html>