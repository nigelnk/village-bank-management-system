<?php

require_once 'config.php';

$conn = get_server_db();

if (!$conn) {
    die("Database connection failed.");
}

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

// create members table
$conn->query("
CREATE TABLE IF NOT EXISTS members (
    member_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(30) NOT NULL,
    lastname VARCHAR(30) NOT NULL,
    phone BIGINT,
    location TEXT,
    next_of_kin BIGINT,
    gender VARCHAR(8),
    status TEXT,
    joined_date DATE,
    updated_at DATE
)");

// create Users table */
$conn->query("
CREATE TABLE IF NOT EXISTS users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    member_id BIGINT,
    role_id BIGINT,
    created_at BIGINT,
    username VARCHAR(30) UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,

    FOREIGN KEY (member_id) REFERENCES members(member_id),
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
)");

// create shares table
$conn->query("
CREATE TABLE IF NOT EXISTS shares (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    member_id BIGINT,
    share BIGINT,
    paid_at DATE,

    FOREIGN KEY (member_id) REFERENCES members(member_id)
)");

// create savings table
$conn->query("
CREATE TABLE IF NOT EXISTS savings (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    member_id BIGINT UNIQUE,
    total_shares BIGINT,
    updated_at BIGINT,

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

// some dumy data

//roles
$conn->query("
INSERT IGNORE INTO roles (role_name) VALUES
('Chairperson'),
('Treasurer'),
('Member'),
('Guest')
");

// members
$conn->query("
INSERT INTO members 
(firstname, lastname, phone, location, next_of_kin, gender, status, joined_date, updated_at)
VALUES
('Fortune', 'Salijen', 265888111111, 'Blantyre', 265888222222, 'Male', 'active', CURDATE(), CURDATE()),
('Blessings', 'Ngaiyaye', 265888333333, 'Zomba', 265888444444, 'Male', 'active', CURDATE(), CURDATE()),
('Alice', 'Ndolo', 265888555555, 'Lilongwe', 265888666666, 'Female', 'active', CURDATE(), CURDATE());
");

// users
$conn->query("
INSERT INTO users (member_id, role_id, created_at, username, password_hash)
VALUES
(1, 1, UNIX_TIMESTAMP(), 'fortune_salijen', '2y$10dsdfewrfsfd'),
(2, 2, UNIX_TIMESTAMP(), 'blessings_ngaiyaye', '2y$10dfderer345ef'),
(3, 3, UNIX_TIMESTAMP(), 'alice_ndolo', '2y$10dfdsdfdsfd');
");

// shares
$conn->query("
INSERT INTO shares (member_id, share, paid_at) VALUES
(1, 5000, '2026-01-05'),
(1, 5000, '2026-02-05'),
(1, 5000, '2026-03-05'),

(2, 3000, '2026-01-05'),
(2, 3000, '2026-02-05'),
(2, 3000, '2026-03-05'),

(3, 2000, '2026-01-05'),
(3, 2000, '2026-02-05'),
(3, 2000, '2026-03-05');
");

//savings
$conn->query("
INSERT INTO savings (member_id, total_shares, updated_at) VALUES
(1, 15000, UNIX_TIMESTAMP()),
(2, 9000, UNIX_TIMESTAMP()),
(3, 6000, UNIX_TIMESTAMP());
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
('loan', 3, 2000, 'IN', '2026-03-20');
");

echo "Database and tables created successfully with dummy data.";

?>
