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
    } elseif (strlen($new) < 6) {
        $error = "New password must be at least 6 characters.";
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
            <div class="panel" style="background:#f8d7da;color:#721c24; margin-bottom:5px;">
                <strong><?= htmlspecialchars($error) ?></strong>
            </div>
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
                    <input type="password" name="new_password" required>
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

            <form method="POST">

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

</body>

</html>
