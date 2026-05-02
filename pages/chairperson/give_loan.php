<?php

require_once 'auth_check.php';

require_once '../../utils/config.php';

$conn = get_db();

$member = $_POST['member_id'];
$amount = $_POST['amount'];
$interest = $_POST['interest'];
$date = $_POST['return_date'];

$conn->query("
INSERT INTO loans (member_id, amount, total_paid, balance, interest, status, date_borrowed)
VALUES ($member, $amount, 0, $amount, $interest, 'active', CURDATE())
");

header("Location: loan_management.php");