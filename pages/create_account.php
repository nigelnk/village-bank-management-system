<?php

$conn = new mysqli("localhost", "root", "", "village_bank");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $phone = $_POST['phone'];
    $home = $_POST['home'];
    $next_kin = $_POST['next_kin'];
    $status = $_POST['status'];
    $date_join = $_POST['date_join'];

    // Prepared statement (safe insert)
    $stmt = $conn->prepare("
        INSERT INTO users 
        (fname, lname, gender, phone, home_address, next_kin, status, join_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssssss",
        $fname,
        $lname,
        $gender,
        $phone,
        $home,
        $next_kin,
        $status,
        $date_join
    );

    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully');</script>";
    } else {
        echo "<script>alert('Error creating account');</script>";
    }

    $stmt->close();
}

$conn->close();

?>
