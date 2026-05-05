<?php

require_once 'config.php';

$conn = get_server_db();

if (!$conn) {
    die("Database connection failed.");
}

try {
    // creating db first
    $conn->query("CREATE DATABASE IF NOT EXISTS village_bank");

    // use the created database
    $conn->select_db("village_bank");

    // create roles table
    $conn->query("
    CREATE TABLE IF NOT EXISTS roles (
        role_id BIGINT AUTO_INCREMENT PRIMARY KEY,
        role_name VARCHAR(12) NOT NULL UNIQUE
    )");

    // users table
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
    // create members table
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

    // create savings table
    $conn->query("
    CREATE TABLE IF NOT EXISTS savings (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        member_id BIGINT UNIQUE,
        total_shares BIGINT,
        updated_at DATE,

        FOREIGN KEY (member_id) REFERENCES members(member_id)
    )");

    // create loans table
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

    // create transactions table
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

    //reports table
   $conn->query("
   CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    file_path VARCHAR(255),
    report_type VARCHAR(50),
    date_generated DATETIME
)");

    // some dumy data

    //roles
    $conn->query("
    INSERT IGNORE INTO roles (role_name) VALUES
    ('Chairperson'),
    ('Treasurer'),
    ('Member'),
    ('Guest')
    ");

    // users
    $conn->query("
        INSERT INTO users (id, role_id, username, password_hash)
        VALUES
        (1, 1, 'fortune_salijen', '2y$10dsdfewrfsfd'),
        (2, 2, 'blessings_ngaiyaye', '2y$10dfderer345ef'),
        (3, 3, 'alice_ndolo', '2y$10dfdsdfdsfd')
        ");

    // members 

    $conn->query("
        INSERT INTO members 
        (user_id, firstname, lastname, phone, location, next_of_kin_phone, next_of_kin_name, relationship, gender, status, joined_date, updated_at)
        VALUES
        (1, 'Fortune', 'Salijen', '265888111111', 'Blantyre', '265888222222', 'John Salijen', 'parent', 'Male', 'approved', CURDATE(), CURDATE()),
        (2, 'Blessings', 'Ngaiyaye', '265888333333', 'Zomba', '265888444444', 'Mary Ngaiyaye', 'sibling', 'Male', 'approved', CURDATE(), CURDATE()),
        (3, 'Alice', 'Ndolo', '265888555555', 'Lilongwe', '265888666666', 'Grace Ndolo', 'parent', 'Female', 'approved', CURDATE(), CURDATE())
        ");

    //savings
    $conn->query("
    INSERT INTO savings (member_id, total_shares, updated_at) VALUES
    (1, 15000, CURDATE()),
    (2, 9000, CURDATE()),
    (3, 6000, CURDATE());
    ");

    // loans
    $conn->query("
    INSERT INTO loans 
    (member_id, amount, total_paid, balance, interest, status, date_borrowed, date_paid)
    VALUES
    (1, 20000, 5000, 15000, 2000, 'active', '2026-02-01', NULL),

    (2, 10000, 10000, 0, 1000, 'paid', '2026-01-10', '2026-03-10'),

    (3, 5000, 2000, 3000, 500, 'active', '2026-03-01', NULL);
    ");

    // transactions
    $conn->query("
    INSERT INTO transactions (type, member_id, amount, direction, transaction_date) VALUES
    ('loan', 1, 5000, 'OUT', '2026-02-01'),
    ('loan', 2, 10000, 'OUT', '2026-01-10'),
    ('loan', 2, 10000, 'IN', '2026-03-10'),
    ('loan', 3, 5000, 'OUT', '2026-03-01'),
    ('loan', 3, 2000, 'IN', '2026-03-20'),
    ('loan', 2, 10000, 'OUT', '2026-01-10'),
    ('loan', 2, 10000, 'IN', '2026-03-10'),
    ('loan', 3, 5000, 'OUT', '2026-03-01'),
    ('loan', 3, 2000, 'IN', '2026-03-20'),
    ('loan', 2, 10000, 'OUT', '2026-01-10'),
    ('loan', 2, 10000, 'IN', '2026-03-10'),
    ('loan', 3, 5000, 'OUT', '2026-03-01'),
    ('loan', 3, 2000, 'IN', '2026-03-20'),
    ('loan', 2, 10000, 'OUT', '2026-01-10'),
    ('loan', 2, 10000, 'IN', '2026-03-10'),
    ('loan', 3, 5000, 'OUT', '2026-03-01'),
    ('loan', 3, 2000, 'IN', '2026-03-20');
    ");

    header("Location: ../auth/login.php");
    exit();
} catch (mysqli_sql_exception $e) {
    // Catch any query errors
    die("Database setup failed: " . $e->getMessage());
}
