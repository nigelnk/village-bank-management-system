<?php
session_start();
require_once '../utils/config.php';

$conn = get_db();

if (!isset($_SESSION['reset_user'])) {
    header("Location: forgot_password.php");
    exit();
}

$user_id = $_SESSION['reset_user'];

$result = $conn->query("SELECT * FROM users WHERE id=$user_id");
$user = $result->fetch_assoc();

$error = "";

if (isset($_POST['answer'])) {
    if (password_verify($_POST['answer'], $user['security_answer_hash'])) {
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Incorrect answer.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Security Check</title>
    <link rel="stylesheet" href="../static/css/auth_forms.css">
</head>

<body>

    <div class="container">

        <img class="logo" src="../static/photos/logo.jpeg">

        <div class="title">
            <h3>Security Question</h3>
        </div>

        <div class="form">

            <h4><?= $user['security_question'] ?></h4>

            <form method="POST">
                <input type="text" name="answer" placeholder="Your Answer" required>
                <button>Verify</button>
            </form>

            <p class="error"><?= $error ?></p>

        </div>

    </div>

</body>

</html>