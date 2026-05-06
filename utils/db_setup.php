<?php

require_once 'config.php';

$conn = get_server_db();

if (!$conn) {
    die("Database connection failed.");
}

try {
    $conn->query("CREATE DATABASE IF NOT EXISTS village_bank");
    $conn->select_db("village_bank");

    /* ================= TABLES ================= */

    $conn->query("
    CREATE TABLE IF NOT EXISTS roles (
        role_id BIGINT AUTO_INCREMENT PRIMARY KEY,
        role_name VARCHAR(12) NOT NULL UNIQUE
    )");

    $conn->query("
    CREATE TABLE IF NOT EXISTS users (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        role_id BIGINT,
        username VARCHAR(30) UNIQUE NOT NULL,
        password_hash TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        security_question VARCHAR(255) NULL,
        security_answer_hash TEXT NULL,
        FOREIGN KEY (role_id) REFERENCES roles(role_id)
    )");

    $conn->query("
    CREATE TABLE IF NOT EXISTS members (
        member_id BIGINT AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNIQUE, 
        firstname VARCHAR(30) NOT NULL,
        lastname VARCHAR(30) NOT NULL,
        phone VARCHAR(20),
        location TEXT,
        next_of_kin_name VARCHAR(50),
        next_of_kin_phone VARCHAR(20),
        relationship VARCHAR(20),
        gender VARCHAR(10),
        status ENUM('pending','approved','rejected') DEFAULT 'pending',
        joined_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $conn->query("
    CREATE TABLE IF NOT EXISTS savings (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        member_id BIGINT UNIQUE,
        total_shares BIGINT,
        updated_at DATE,
        FOREIGN KEY (member_id) REFERENCES members(member_id)
    )");

    $conn->query("
    CREATE TABLE IF NOT EXISTS loans (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        member_id BIGINT,
        amount BIGINT,
        total_paid BIGINT,
        balance BIGINT,
        interest BIGINT,
        status VARCHAR(10),
        date_borrowed DATE,
        date_paid DATE,
        FOREIGN KEY (member_id) REFERENCES members(member_id)
    )");

    $conn->query("
    CREATE TABLE IF NOT EXISTS transactions (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        type VARCHAR(10),
        member_id BIGINT,
        amount BIGINT,
        direction VARCHAR(3),
        transaction_date DATE,
        FOREIGN KEY (member_id) REFERENCES members(member_id)
    )");

    $conn->query("
    CREATE TABLE IF NOT EXISTS reports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        file_path VARCHAR(255),
        report_type VARCHAR(50),
        date_generated DATETIME
    )");

    /* ================= ROLES ================= */

    $conn->query("
    INSERT IGNORE INTO roles (role_id, role_name) VALUES
    (1,'Chairperson'),
    (2,'Treasurer'),
    (3,'Member'),
    (4,'Guest')
    ");

    /* ================= USERS ================= */

    for ($i = 1; $i <= 20; $i++) {
        $role = ($i == 1) ? 1 : (($i == 2) ? 2 : 3);
        $username = "user_$i";
        $password = "hash_$i";

        $conn->query("
        INSERT INTO users (id, role_id, username, password_hash)
        VALUES ($i, $role, '$username', '$password')
        ");
    }

    /* ================= MEMBERS ================= */

    $locations = ['Lilongwe','Blantyre','Zomba','Mzuzu'];

    for ($i = 1; $i <= 20; $i++) {
        $loc = $locations[array_rand($locations)];
        $gender = ($i % 2 == 0) ? 'Female' : 'Male';

        $conn->query("
        INSERT INTO members 
        (user_id, firstname, lastname, phone, location, next_of_kin_name, next_of_kin_phone, relationship, gender, status, joined_date, updated_at)
        VALUES
        ($i, 'First$i', 'Last$i', '26588$i$i$i$i$i', '$loc',
        'Kin$i', '26599$i$i$i$i$i', 'relative', '$gender',
        'approved', CURDATE(), CURDATE())
        ");
    }

    /* ================= SAVINGS ================= */

    for ($i = 1; $i <= 20; $i++) {
        $shares = rand(2000, 20000);

        $conn->query("
        INSERT INTO savings (member_id, total_shares, updated_at)
        VALUES ($i, $shares, CURDATE())
        ");
    }

    /* ================= LOANS ================= */

    for ($i = 1; $i <= 10; $i++) {
        $amount = rand(5000, 30000);
        $paid = rand(0, $amount);
        $balance = $amount - $paid;
        $status = ($balance == 0) ? 'paid' : 'active';

        $conn->query("
        INSERT INTO loans
        (member_id, amount, total_paid, balance, interest, status, date_borrowed, date_paid)
        VALUES
        ($i, $amount, $paid, $balance, 1000, '$status',
        DATE_SUB(CURDATE(), INTERVAL $i MONTH),
        " . ($status == 'paid' ? "CURDATE()" : "NULL") . ")
        ");
    }

    /* ================= TRANSACTIONS (30+) ================= */

    for ($i = 1; $i <= 35; $i++) {
        $member = rand(1, 20);
        $amount = rand(1000, 15000);
        $direction = (rand(0,1)) ? 'IN' : 'OUT';
        $type = 'loan';

        $conn->query("
        INSERT INTO transactions (type, member_id, amount, direction, transaction_date)
        VALUES ('$type', $member, $amount, '$direction',
        DATE_SUB(CURDATE(), INTERVAL $i DAY))
        ");
    }

    header("Location: ../auth/login.php");
    exit();

} catch (mysqli_sql_exception $e) {
    die("Database setup failed: " . $e->getMessage());
}