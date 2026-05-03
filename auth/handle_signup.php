<?php
require_once '../utils/config.php';
$conn = get_db();

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// get form data
$username = $_POST['username'];
$password = $_POST['password'];
$password2 = $_POST['password2'];

// check passwords match
if ($password !== $password2) {
    die("Passwords do not match.");
}

// hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// get guest role id
$result = $conn->query("SELECT role_id FROM roles WHERE role_name = 'Guest'");

if ($result->num_rows == 0) {
    die("Guest role not found. Please insert it first.");
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
    header("Location: ./complete_profile.php?user_id=$user_id");
    exit();

} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
