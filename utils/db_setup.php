<?php

require_once 'config.php';

$conn = get_server_db();

if (!$conn) {
    die("Database connection failed.");
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    /* ================= DATABASE ================= */

    $conn->query("CREATE DATABASE IF NOT EXISTS village_bank");
    $conn->select_db("village_bank");

    /* ================= TABLES ================= */

    $conn->query("
    CREATE TABLE IF NOT EXISTS roles (
        role_id BIGINT AUTO_INCREMENT PRIMARY KEY,
        role_name VARCHAR(20) NOT NULL UNIQUE
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
        location VARCHAR(100),
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
        type VARCHAR(20),
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
    (1, 'Chairperson'),
    (2, 'Treasurer'),
    (3, 'Member'),
    (4, 'Guest')
    ");

    /* ================= USERS ================= */

    $conn->query("
    INSERT IGNORE INTO users (id, role_id, username, password_hash) VALUES

    (1,1,'grace_banda','hash_1'),
    (2,2,'patrick_phiri','hash_2'),
    (3,3,'john_mwale','hash_3'),
    (4,3,'memory_chilima','hash_4'),
    (5,3,'thoko_kasambara','hash_5'),
    (6,3,'agnes_mhone','hash_6'),
    (7,3,'brighton_kumwenda','hash_7'),
    (8,3,'chimwemwe_jere','hash_8'),
    (9,3,'loveness_benjamin','hash_9'),
    (10,3,'george_mvula','hash_10'),
    (11,3,'tamanda_msiska','hash_11'),
    (12,3,'frank_gondwe','hash_12'),
    (13,3,'ruth_nyirenda','hash_13'),
    (14,3,'daniel_kalua','hash_14'),
    (15,3,'mercy_masina','hash_15'),
    (16,3,'charles_zimba','hash_16'),
    (17,3,'edith_mkandawire','hash_17'),
    (18,3,'steven_mbewe','hash_18'),
    (19,3,'triza_mponda','hash_19'),
    (20,3,'ivan_chisale','hash_20')
    ");

    /* ================= MEMBERS ================= */

    $conn->query("
    INSERT IGNORE INTO members
    (member_id, user_id, firstname, lastname, phone, location,
    next_of_kin_name, next_of_kin_phone, relationship,
    gender, status, joined_date, updated_at)

    VALUES

    (1,1,'Grace','Banda','0991123456','Area 25, Lilongwe',
    'Peter Banda','0888123001','Husband',
    'Female','approved','2024-01-10',NOW()),

    (2,2,'Patrick','Phiri','0882456712','Chilomoni, Blantyre',
    'Mary Phiri','0999456123','Wife',
    'Male','approved','2024-01-12',NOW()),

    (3,3,'John','Mwale','0993345678','Mzuzu City',
    'Agnes Mwale','0887123456','Mother',
    'Male','approved','2024-01-15',NOW()),

    (4,4,'Memory','Chilima','0888345678','Zomba Central',
    'Ruth Mvula','0991239876','Sister',
    'Female','approved','2024-01-18',NOW()),

    (5,5,'Thoko','Kasambara','0997654321','Area 18, Lilongwe',
    'Luka Kasambara','0887654321','Brother',
    'Female','approved','2024-01-20',NOW()),

    (6,6,'Agnes','Mhone','0888765432','Bangwe, Blantyre',
    'Yamikani Mhone','0998877665','Son',
    'Female','approved','2024-01-25',NOW()),

    (7,7,'Brighton','Kumwenda','0993344556','Katoto, Mzuzu',
    'Tadala Kumwenda','0883344556','Wife',
    'Male','approved','2024-02-01',NOW()),

    (8,8,'Chimwemwe','Jere','0889988776','Area 49, Lilongwe',
    'Ester Jere','0999988776','Mother',
    'Male','approved','2024-02-05',NOW()),

    (9,9,'Loveness','Benjamin','0998877445','Chirimba, Blantyre',
    'Hilda Benjamin','0888877445','Sister',
    'Female','approved','2024-02-08',NOW()),

    (10,10,'George','Mvula','0887766554','Luwinga, Mzuzu',
    'Martha Mvula','0997766554','Wife',
    'Male','approved','2024-02-10',NOW()),

    (11,11,'Tamanda','Msiska','0996655443','Area 36, Lilongwe',
    'Joseph Msiska','0886655443','Father',
    'Female','approved','2024-02-14',NOW()),

    (12,12,'Frank','Gondwe','0885544332','Ndirande, Blantyre',
    'Susan Gondwe','0995544332','Wife',
    'Male','approved','2024-02-16',NOW()),

    (13,13,'Ruth','Nyirenda','0994433221','Chancellor College, Zomba',
    'Moses Nyirenda','0884433221','Brother',
    'Female','approved','2024-02-20',NOW()),

    (14,14,'Daniel','Kalua','0883322110','Area 23, Lilongwe',
    'Lina Kalua','0993322110','Mother',
    'Male','approved','2024-02-22',NOW()),

    (15,15,'Mercy','Masina','0992211334','Kanjedza, Blantyre',
    'Grace Masina','0882211334','Aunt',
    'Female','approved','2024-02-25',NOW()),

    (16,16,'Charles','Zimba','0881199887','Mzuzu University Area',
    'Lucy Zimba','0991199887','Wife',
    'Male','approved','2024-03-01',NOW()),

    (17,17,'Edith','Mkandawire','0997766112','Likangala, Zomba',
    'Victor Mkandawire','0887766112','Brother',
    'Female','approved','2024-03-05',NOW()),

    (18,18,'Steven','Mbewe','0886655991','Area 47, Lilongwe',
    'Tionge Mbewe','0996655991','Father',
    'Male','approved','2024-03-08',NOW()),

    (19,19,'Triza','Mponda','0995544882','Limbe, Blantyre',
    'James Mponda','0885544882','Husband',
    'Female','approved','2024-03-10',NOW()),

    (20,20,'Ivan','Chisale','0884433771','Chibavi, Mzuzu',
    'Henry Chisale','0994433771','Father',
    'Male','approved','2024-03-12',NOW())
    ");

    /* ================= SAVINGS ================= */

    $conn->query("
    INSERT IGNORE INTO savings (member_id, total_shares, updated_at) VALUES

    (1,120000,CURDATE()),
    (2,95000,CURDATE()),
    (3,87000,CURDATE()),
    (4,143000,CURDATE()),
    (5,68000,CURDATE()),
    (6,74000,CURDATE()),
    (7,51000,CURDATE()),
    (8,99000,CURDATE()),
    (9,81000,CURDATE()),
    (10,134000,CURDATE()),
    (11,56000,CURDATE()),
    (12,92000,CURDATE()),
    (13,79000,CURDATE()),
    (14,61000,CURDATE()),
    (15,88000,CURDATE()),
    (16,45000,CURDATE()),
    (17,73000,CURDATE()),
    (18,101000,CURDATE()),
    (19,66000,CURDATE()),
    (20,97000,CURDATE())
    ");

    /* ================= LOANS ================= */

    $conn->query("
    INSERT IGNORE INTO loans
    (member_id, amount, total_paid, balance, interest, status, date_borrowed, date_paid)

    VALUES

    (1,150000,50000,100000,15000,'active','2025-01-10',NULL),
    (3,80000,80000,0,8000,'paid','2024-10-05','2025-02-01'),
    (5,120000,45000,75000,12000,'active','2025-02-14',NULL),
    (7,60000,60000,0,6000,'paid','2024-08-01','2025-01-15'),
    (9,200000,75000,125000,20000,'active','2025-03-02',NULL),
    (12,90000,90000,0,9000,'paid','2024-09-11','2025-01-25'),
    (14,110000,40000,70000,11000,'active','2025-01-28',NULL),
    (16,70000,30000,40000,7000,'active','2025-02-18',NULL),
    (18,50000,50000,0,5000,'paid','2024-11-01','2025-03-01'),
    (20,130000,60000,70000,13000,'active','2025-03-15',NULL)
    ");

    /* ================= TRANSACTIONS (30+) ================= */

    $conn->query("
    INSERT INTO transactions
    (type, member_id, amount, direction, transaction_date)

    VALUES

    ('saving',1,10000,'IN','2025-03-01'),
    ('loan',1,150000,'OUT','2025-03-02'),
    ('saving',2,7000,'IN','2025-03-03'),
    ('saving',3,8500,'IN','2025-03-04'),
    ('loan_payment',3,12000,'IN','2025-03-05'),
    ('saving',4,5000,'IN','2025-03-06'),
    ('saving',5,15000,'IN','2025-03-07'),
    ('loan',5,120000,'OUT','2025-03-08'),
    ('saving',6,6000,'IN','2025-03-09'),
    ('saving',7,4000,'IN','2025-03-10'),
    ('loan_payment',7,10000,'IN','2025-03-11'),
    ('saving',8,9000,'IN','2025-03-12'),
    ('saving',9,11000,'IN','2025-03-13'),
    ('loan',9,200000,'OUT','2025-03-14'),
    ('saving',10,13000,'IN','2025-03-15'),
    ('saving',11,7500,'IN','2025-03-16'),
    ('saving',12,14000,'IN','2025-03-17'),
    ('loan_payment',12,15000,'IN','2025-03-18'),
    ('saving',13,6500,'IN','2025-03-19'),
    ('saving',14,9800,'IN','2025-03-20'),
    ('loan',14,110000,'OUT','2025-03-21'),
    ('saving',15,7200,'IN','2025-03-22'),
    ('saving',16,8900,'IN','2025-03-23'),
    ('loan',16,70000,'OUT','2025-03-24'),
    ('saving',17,5600,'IN','2025-03-25'),
    ('saving',18,10000,'IN','2025-03-26'),
    ('loan_payment',18,8000,'IN','2025-03-27'),
    ('saving',19,4700,'IN','2025-03-28'),
    ('saving',20,12500,'IN','2025-03-29'),
    ('loan',20,130000,'OUT','2025-03-30'),
    ('saving',3,6000,'IN','2025-04-01'),
    ('saving',5,7500,'IN','2025-04-02'),
    ('loan_payment',5,10000,'IN','2025-04-03'),
    ('saving',9,9200,'IN','2025-04-04'),
    ('saving',14,8700,'IN','2025-04-05')
    ");

    header("Location: ../auth/login.php");
    exit();

} catch (mysqli_sql_exception $e) {

    die("Database setup failed: " . $e->getMessage());
}
?>