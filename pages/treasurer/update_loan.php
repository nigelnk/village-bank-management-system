<?php

require_once '../../utils/config.php';

$conn = get_db();

$id = (int) $_POST['loan_id'];
$paid = (int) $_POST['amount_paid'];
$interest = (int) $_POST['interest'];

// fetch loan first
$loanQuery = $conn->query("
SELECT * FROM loans
WHERE id = $id
");

$loan = $loanQuery->fetch_assoc();

if (!$loan) {
    die("Loan not found");
}

$newTotalPaid = $loan['total_paid'] + $paid;
$newBalance = $loan['balance'] - $paid;

$status = 'active';
$datePaid = "NULL";

// fully paid
if ($newBalance <= 0) {
    $newBalance = 0;
    $status = 'paid';
    $datePaid = "'" . date('Y-m-d') . "'";
}

// update loan
$conn->query("
UPDATE loans
SET
    total_paid = $newTotalPaid,
    balance = $newBalance,
    interest = $interest,
    status = '$status',
    date_paid = $datePaid
WHERE id = $id
");

// insert repayment transaction
$conn->query("
INSERT INTO transactions (
    type,
    member_id,
    amount,
    direction,
    transaction_date
)
VALUES (
    'loan',
    {$loan['member_id']},
    $paid,
    'IN',
    CURDATE()
)
");

header("Location: loan_management.php");
exit();
