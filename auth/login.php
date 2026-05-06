<?php
session_start();
require_once '../utils/config.php';

$conn = get_server_db();

if (!$conn) {
    $_SESSION["error_message"] = "Database connection failed.";
    header("Location: login.php");
    die();
}

$conn->select_db("village_bank");


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    //For persisting username in username field
    $_SESSION['old_username'] = $username;

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
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role_name'];

            // role-based redirection
            switch ($user['role_name']) {

                case 'Chairperson':
                    header("Location: ../pages/chairperson/dashboard.php");
                    $stmt = $conn->prepare("
                        SELECT member_id 
                        FROM members 
                        WHERE user_id = ?
                        LIMIT 1
                    ");
                    $stmt->bind_param("i", $user['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($row = $result->fetch_assoc()) {
                        $_SESSION['member_id'] = $row['member_id'];
                    }
                    exit();
                    

                case 'Treasurer':
                    header("Location: ../pages/treasurer/transactions.php");
                    $stmt = $conn->prepare("
                        SELECT member_id 
                        FROM members 
                        WHERE user_id = ?
                        LIMIT 1
                    ");
                    $stmt->bind_param("i", $user['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($row = $result->fetch_assoc()) {
                        $_SESSION['member_id'] = $row['member_id'];
                    }
                    exit();

                case 'Member':
                    $stmt = $conn->prepare("
                        SELECT member_id 
                        FROM members 
                        WHERE user_id = ?
                        LIMIT 1
                    ");
                    $stmt->bind_param("i", $user['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($row = $result->fetch_assoc()) {
                        $_SESSION['member_id'] = $row['member_id'];
                    }

                    header("Location: ../pages/member/dashboard.php");
                    
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

                        header("Location: complete_profile.php?user_id=$user_id");
                        exit();
                    }

                    $member = $check->fetch_assoc();

                    if ($member['status'] === 'pending') {
                        header("Location: waiting_approval.php");
                        exit();
                    }

                    if ($member['status'] === 'approved') {
                        header("Location: ../pages/member/dashboard.php");
                        exit();
                    }

                    // fallback
                    $_SESSION["error_message"] = "Login failed! Please try again.";
                    header("Location: login.php");
                    exit();

                break;

                default:
                    $_SESSION["error_message"] = "Login failed! Please try again.";
                    header("Location: login.php");
                    break;
            }

            exit();
        } else {
            // wrong password
            $_SESSION["error_message"] = "Invalid credentials.";
            header("Location: login.php");
            exit();
        }
    } else {
        // user not found
        $_SESSION["error_message"] = "Invalid credentials.";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Village Bank Login</title>

    <link rel="stylesheet" href="../static/css/signin.css">

</head>

<body>

    <div class="container">

        <div class="subtitle">
            <img class="logo" src="../static/photos/logo.jpeg" alt="Village Bank Logo">
        </div>
        <div class="title">
            <h3>WELCOME TO NANSADI VILLAGE BANK</h3>
        </div>
        
        <div>
            <?php 
            if (isset($_SESSION["error_message"])) {
                $error_message = $_SESSION["error_message"];
                echo "<p class='error-message'>$error_message</p>";
                unset($_SESSION["error_message"]);
            }
            ?>
        </div>

        <div class="form">

            <form method="post" action="login.php">

                <div class="username">
                <input type="text" name="username" required placeholder="username" value="<?php if (isset($_SESSION['old_username'])) { echo $_SESSION['old_username']; unset($_SESSION['old_username']);} ?>"> <!-- Persisting old username -->
                </div>
                <br>

                <div class="pass">
                    <input id="password" type="password" name="password" required placeholder="Enter password">
                    <button type="button" id="togglePassword">Show</button>
                </div>
                <br>

                <div class="login">
                    <input type="submit" value="Log In">
                </div>
                <br>

            </form>

            <div class="support">

                <div class="forgotpas">
                    <a href="forgot_password.php">Forgot Password?</a>
                </div>
                <br>

                <div class="create">
                    <a href="signup.php">Create New Account</button>
                </div>

            </div>

        </div>

    </div>
        
    <script>
        const toggleBtn = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");

        toggleBtn.addEventListener("click", function () {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleBtn.textContent = "Hide";
            } else {
                passwordInput.type = "password";
                toggleBtn.textContent = "Show";
            }
        }); 
    </script>

</body>

</html>

<?php 
exit();
?>
