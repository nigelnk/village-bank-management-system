<?php
require_once '../../auth_check.php';

require '../../utils/config.php';

$conn = get_db();

$id = $_POST['loan_id'];
$paid = $_POST['amount_paid'];
$interest = $_POST['interest'];

$conn->query("
UPDATE loans 
SET total_paid = total_paid + $paid,
balance = balance - $paid,
interest = $interest
WHERE id = $id
");

header("Location: loan_management.php");