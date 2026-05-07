<?php
require_once '../../auth_check.php';
requireRole(['Member']);

require_once "../../utils/config.php";
$conn = get_db();

$user_id = $_SESSION["user_id"];

/* GET USER NAME */
$stmt = $conn->prepare("SELECT firstname FROM members WHERE member_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

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
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result || !password_verify($current, $result['password_hash'])) {
            $error = "Current password is incorrect.";
        } else {

            $newHash = password_hash($new, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
            $update->bind_param("si", $newHash, $user_id);

            if ($update->execute()) {
                $message = "Password updated successfully!";
            } else {
                $error = "Failed to update password.";
            }
        }
    }
}

if (isset($_POST['save_security'])) {

    $question = trim($_POST['question'] ?? '');
    $answer = strtolower(trim($_POST['answer'] ?? ''));

    if (empty($question) || empty($answer)) {
        $error = "All fields are required.";
    } else {

        $answerHash = password_hash($answer, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            UPDATE users 
            SET security_question=?, security_answer_hash=? 
            WHERE id=?
        ");

        $stmt->bind_param("ssi", $question, $answerHash, $user_id);

        if ($stmt->execute()) {
            $message = "Security question saved!";
        } else {
            $error = "Failed to save security question.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<title>Manage Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #eef1f5;
}

.container {
    min-height: 100vh;
}
.topbar {
    width: 100%;
    height: 100px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 34px;
    background: #1F4D36;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.topbar-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.welcome {
    font-weight: bold;
    font-size: 18px;
    color: #FDD017;
}

.main {
    width: 75vw;
    max-width: 900px;
    margin: 30px auto;
}

/* CARD */
.card {
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
}

.form-box {
    margin-bottom: 25px;
}

.form-box h3 {
    margin-bottom: 15px;
    color: #1F4D36;
}

.form-group {
    margin-bottom: 15px;
}

label {
    font-size: 14px;
    color: #333;
    font-weight: 500;
}

input, select {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

input:focus, select:focus {
    border-color: #1F4D36;
    outline: none;
}


button {
    width: 100%;
    padding: 10px;
    background: #1F4D36;
    color: white;
    border: none;
    border-radius: 20px;
    cursor: pointer;
}

button:hover {
    background: #163728;
}

/* ALERTS */
.panel {
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 10px;
}

.success {
    background: #d1fae5;
    color: #065f46;
}

.error {
    background: #fee2e2;
    color: #991b1b;
}
</style>

</head>

<body>

<div class="container">

    <header class="topbar">
        <div class="topbar-left">
            <img src="../../static/photos/IMG-20260501-WA0108.jpg" width="40" height="40" style="border-radius:50%;">
            <div class="welcome">
                Welcome, <?= htmlspecialchars($user['firstname'] ?? 'User') ?>
            </div>
        </div>

        <div>
            <a href="../dashboard.php"><button>Dashboard</button></a>
            <a href="../../auth/login.php"><button style="background:#ff4d4f;">Logout</button></a>
        </div>
    </header>

    <div class="main">

        <div class="card">

            <h2 style="margin-bottom:15px; color:#1F4D36;">Manage Password</h2>

            <?php if ($message): ?>
                <div class="panel success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="panel error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- CHANGE PASSWORD -->
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
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>

                    <button type="submit" name="change_password">Update Password</button>
                </form>
            </div>

            <div class="form-box">
                <h3>Security Question</h3>

                <form method="POST">
                    <div class="form-group">
                        <label>Question</label>
                        <select name="question" required>
                            <option value="">-- Select --</option>
                            <option value="What is your mother's name?">Mother's name</option>
                            <option value="What is your first school?">First school</option>
                            <option value="What is your favorite food?">Favorite food</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Answer</label>
                        <input type="text" name="answer" required>
                    </div>

                    <button type="submit" name="save_security">Save</button>
                </form>
            </div>

        </div>
    </div>

</div>

</body>
</html>