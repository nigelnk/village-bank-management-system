<?php
require_once __DIR__ . '/../auth_check.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../utils/config.php';
$conn = get_db();

$user_id = $_SESSION['user_id'];

$message = "";
$error = "";

// change password
if (isset($_POST['change_password'])) {

    $current = trim($_POST['current_password'] ?? '');
    $new = trim($_POST['new_password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    if (empty($current) || empty($new) || empty($confirm)) {
        $error = "All fields are required.";
    } elseif ($new !== $confirm) {
        $error = "Passwords do not match.";
    } else {

        $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id=?");

        if (!$stmt) {
            $error = "System error. Try again later.";
        } else {

            $stmt->bind_param("i", $user_id);

            if (!$stmt->execute()) {
                $error = "Failed to verify user.";
            } else {

                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    $error = "User not found.";
                } else {

                    $user = $result->fetch_assoc();

                    if (!password_verify($current, $user['password_hash'])) {
                        $error = "Current password is incorrect.";
                    } else {

                        $newHash = password_hash($new, PASSWORD_DEFAULT);

                        $update = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");

                        if (!$update) {
                            $error = "Failed to prepare update.";
                        } else {

                            $update->bind_param("si", $newHash, $user_id);

                            if ($update->execute()) {
                                $message = "Password updated successfully!";
                            } else {
                                $error = "Failed to update password.";
                            }
                        }
                    }
                }
            }
        }
    }
}

// security question
if (isset($_POST['save_security'])) {

    $question = trim($_POST['question'] ?? '');
    $answer = strtolower(trim($_POST['answer'] ?? ''));

    if (empty($question) || empty($answer)) {
        $error = "All fields are required.";
    } elseif (strlen($answer) < 3) {
        $error = "Answer is too short.";
    } else {

        $answerHash = password_hash($answer, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            UPDATE users 
            SET security_question=?, security_answer_hash=? 
            WHERE id=?
        ");

        if (!$stmt) {
            $error = "Failed to prepare security update.";
        } else {

            $stmt->bind_param("ssi", $question, $answerHash, $user_id);

            if ($stmt->execute()) {
                $message = "Security question saved!";
            } else {
                $error = "Failed to save security question.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Password Management</title>

    <link rel="stylesheet" href="../../static/css/chairperson_layout.css">
    <link rel="stylesheet" href="../../static/css/chairperson_sidebar.css">
    <link rel="stylesheet" href="../../static/css/chairperson_topbar.css">
    <link rel="stylesheet" href="../../static/css/styles.css">
    <link rel="stylesheet" href="../../static/css/password.css">

</head>

<body>

    <div class="main">

        <!-- succss message -->
        <?php if ($message) { ?>
            <div class="panel" style="background:#d4edda;color:#155724; margin-bottom:5px;">
                <strong><?= htmlspecialchars($message) ?></strong>
            </div>
        <?php } ?>

        <!-- error msg -->
            <?php if ($error) { ?>
            <p  class="panel" style="background:#f8d7da;color:#721c24; margin-bottom:5px;">
                <strong ><?= htmlspecialchars($error) ?></strong>
            </p>
            <?php } ?>

        <!-- change password section -->
        <div class="form-box">
            <h3>Change Password</h3>

            <form method="POST">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input id="password" type="password" name="new_password" required>
                    <p id="passwordFeedback"></p>
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required>
                </div>

                <button type="submit" name="change_password">Update Password</button>
            </form>
        </div>

        <!-- setting security question-->
        <div class="form-box">
            <h3>Security Question for password reset</h3>

            <form id="form" method="POST">

                <div class="form-group">
                    <label>Select Question</label>
                    <select name="question" required>
                        <option value="">-- Select a question --</option>
                        <option value="What is your mother's name?">Mother's name</option>
                        <option value="What is your first school?">First school</option>
                        <option value="What is your favorite food?">Favorite food</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Your Answer</label>
                    <input type="text" name="answer" required>
                </div>

                <button type="submit" name="save_security">Save Question</button>
            </form>
        </div>

    </div>

    <script>
        const passwordInput = document.getElementById("password");
        const feedback = document.getElementById("passwordFeedback");
        const form = document.getElementById("form");


        passwordInput.addEventListener("blur", validatePassword);
        // passwordInput1.addEventListener("input", validatePassword);

        function validatePassword() {
            const password = passwordInput.value;

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
            // feedback.style.color = "green";
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

    </script>
</body>

</html>
