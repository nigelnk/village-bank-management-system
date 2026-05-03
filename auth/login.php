<?php
session_start();
require_once '../utils/config.php';

$conn = get_server_db();

if (!$conn) {
    die("Database connection failed.");
}

$conn->select_db("village_bank");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("
        SELECT u.id, u.username, u.password_hash, r.role_name
        FROM users u
        JOIN roles r ON u.role_id = r.role_id
        WHERE u.username = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password_hash'])) {

            // Store session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role_name'];

            // role-based redirection
            switch ($user['role_name']) {

                case 'Chairperson':
                    header("Location: ../../pages/chairperson/dashboard.php");
                    exit();
                    

                case 'Treasurer':
                    header("Location: ../../pages/treasurer/dashboard.php");
                    exit();

                case 'Member':
                    header("Location: ../../pages/member/dashboard.php");
                    exit();
                case 'Guest':

                    $user_id = $user['id'];

                    // check if profile exists
                    $check = $conn->query("
                        SELECT status 
                        FROM members 
                        WHERE user_id = '$user_id'
                        LIMIT 1
                    ");

                    if ($check->num_rows === 0) {

                        header("Location: ../auth/complete_profile.php?user_id=$user_id");
                        exit();
                    }

                    $member = $check->fetch_assoc();

                    if ($member['status'] === 'pending') {
                        header("Location: ../auth/waiting_approval.php");
                        exit();
                    }

                    if ($member['status'] === 'approved') {
                        header("Location: ../../pages/member/dashboard.php");
                        exit();
                    }

                    // fallback
                    header("Location: login.php?error=unknown_status");
                    exit();

                break;

                default:
                    header("Location: login.php?error=unknown_role");
                    break;
            }

            exit();
        } else {
            // wrong password
            header("Location: login.php?error=invalid_password");
            exit();
        }
    } else {
        // user not found
        header("Location: login.php?error=user_not_found");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Village Bank Login</title>

    <link rel="stylesheet" href="../../static/css/signin.css">

</head>

<body>

    <div class="container">

        <div class="subtitle">
            <img src="../../static/photos/logo.jpeg" alt="Village Bank Logo">
        </div>
        <div class="title">
            <h3>WELCOME TO NANSADI VILLAGE BANK</h3>
        </div>

        <div class="form">

            <form method="post" action="login.php">

                <div class="username">
                    <input type="text" name="username" required placeholder="username">
                </div>
                <br>

                <div class="pass">
                    <input type="password" name="password" required placeholder="Enter password">
                </div>
                <br>

                <div class="login">
                    <input type="submit" value="Log In">
                </div>
                <br>

            </form>

            <div class="support">

                <div class="forgotpas">
                    <button type="button">Forgot Password?</button>
                </div>
                <br>

                <div class="create">
                    <a href="signup.php">Create New Account</button>
                </div>

            </div>

        </div>

    </div>

</body>

</html>