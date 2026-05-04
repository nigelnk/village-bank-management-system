<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Village Bank Sign Up</title>

    <link rel="stylesheet" href="../static/css/signin.css">

</head>

<body>

    <div class="container">
        <div class="subtitle">
            <img class="logo" src="../static/photos/logo.jpeg" alt="Village Bank Logo">
        </div>

        <div class="title">
            <h3>Create account</h3>
        </div>

        <div >
            <?php 
            if (isset($_SESSION["error_message"])) {
                $error_message = $_SESSION["error_message"];
                echo "<p class='error-message'>$error_message</p>"; 
                unset($_SESSION["error_message"]);
            }
            ?>
        </div>


        <div class="form">

            <form method="post" action="signup.php">

                <div class="username">
                    <input type="text" name="username" required placeholder="Username">
                </div>

                <div class="pass">
                    <input type="password" name="password" required placeholder="Enter Password">
                </div> 
                
                 <div class="pass">
                    <input type="password" name="password2" required placeholder="Confirm Password">
                </div>

                <div class="signup">
                    <input type="submit" value="Sign Up">
                </div>

            </form>

            <div class="support">


                <div class="create">
                    <button type="button"><a href="login.php">Log In</a></button>
                </div>

            </div>

        </div>

    </div>

</body>

</html>

<?php

require_once '../utils/config.php';
$conn = get_db();

// check connection
if ($conn->connect_error) {
    $_SESSION["error_message"] = "Failed to connect to database.";
    header("Location: signup.php");
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // get form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    // check passwords match
    if ($password !== $password2) {
        $_SESSION["error_message"] = "Passwords do not match.";
        header("Location: signup.php");
        die();
    }

    // hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // get guest role id
    $result = $conn->query("SELECT role_id FROM roles WHERE role_name = 'Guest'");

    if ($result->num_rows == 0) {
        $_SESSION["error_message"] = "Guest role not found. Please insert it first.";
        header("Location: signup.php");
        die();
    }

    $row = $result->fetch_assoc();
    $role_id = $row['role_id'];

    // insert user
    $sql = "INSERT INTO users (role_id, created_at, username, password_hash)
            VALUES ('$role_id', NOW(), '$username', '$password_hash')";

    if ($conn->query($sql) === TRUE) {

        // get inserted user id
        $user_id = $conn->insert_id;

        // redirect to complete form
        $_SESSION["user_id"] = $user_id;
        header("Location: complete_profile.php?user_id=$user_id");
        exit();

    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}

exit();
?>
