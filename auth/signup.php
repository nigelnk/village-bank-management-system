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

            <form id="form" method="post" action="signup.php">

                <div class="username">
                    <input type="text" name="username" required placeholder="Username">
                </div>

                <div id="password" class="pass">
                    <input id="password1" type="password" name="password" required placeholder="Enter Password">
                    <button type="button" id="togglePassword1">Show</button>
                </div> 

                <p id="password-feedback" class="strength-message"></p>
                
                 <div class="pass">
                    <input id="password2" type="password" name="password2" required placeholder="Confirm Password">
                    <button type="button" id="togglePassword2">Show</button>
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
    <script>
        /*    Password strength validation (using regex patterns)  */
        // Hard to explain  
        const passwordInput1 = document.getElementById("password1");
        const passwordInput2 = document.getElementById("password2");

        const toggleBtn1 = document.getElementById("togglePassword1");
        const toggleBtn2 = document.getElementById("togglePassword2");

        const feedback = document.getElementById("password-feedback");
        const form = document.getElementById("form");

        passwordInput1.addEventListener("blur", validatePassword);
        // passwordInput1.addEventListener("input", validatePassword);

        function validatePassword() {
            const password = passwordInput1.value;

            if (password.length < 8) {
                showError("At least 8 characters");
                return false;
            }

            if (!/[A-Z]/.test(password)) {
                showError("Add an uppercase letter");
                return false;
            }

            if (!/[a-z]/.test(password)) {
                showError("Add a lowercase letter");
                return false;
            }

            if (!/[0-9]/.test(password)) {
                showError("Add a number");
                return false;
            }

            if (!/[!@#$%^&*()_+\-=\[\]{};:'"\\|,.<>\/?]/.test(password)) {
                showError("Add a special character");
                return false;
            }

            // If everything passes
            feedback.textContent = "Strong password ✅";
            feedback.style.color = "green";
            return true;
        }

        function showError(message) {
            feedback.textContent = message;
            feedback.style.color = "red";
        }

        // Block form submission
        form.addEventListener("submit", function (e) {
            if (!validatePassword()) {
                e.preventDefault();
            }
        });

        // Show/Hide password
        toggleBtn1.addEventListener("click", function () {
            if (passwordInput1.type === "password") {
                passwordInput1.type = "text";
                toggleBtn1.textContent = "Hide";
            } else {
                passwordInput1.type = "password";
                toggleBtn1.textContent = "Show";
            }
        }); 

        toggleBtn2.addEventListener("click", function () {
            if (passwordInput2.type === "password") {
                passwordInput2.type = "text";
                toggleBtn2.textContent = "Hide";
            } else {
                passwordInput2.type = "password";
                toggleBtn2.textContent = "Show";
            }
        }); 
    </script>
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
