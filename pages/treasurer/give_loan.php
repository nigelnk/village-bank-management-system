<?php

require_once '../../utils/config.php';

$conn = get_db();

$member = (int) $_POST['member_id'];
$amount = (int) $_POST['amount'];
$interest = (int) $_POST['interest'];

// calculate available balance
$balanceQuery = "
SELECT
    COALESCE(SUM(CASE WHEN direction='IN' THEN amount ELSE 0 END), 0)
    -
    COALESCE(SUM(CASE WHEN direction='OUT' THEN amount ELSE 0 END), 0)
    AS available_balance
FROM transactions
";

$availableBalance = $conn->query($balanceQuery)
                         ->fetch_assoc()['available_balance'];

// stop loan if insufficient funds
if ($amount > $availableBalance) {

    echo "
    <script>
        alert('Insufficient available balance.');
        window.location='loan_management.php';
    </script>
    ";

    exit();
}

// calculate total repayable
$totalRepayable = $amount + (($interest / 100) * $amount);

// insert into loans table
$conn->query("
INSERT INTO loans (
    member_id,
    amount,
    total_paid,
    balance,
    interest,
    status,
    date_borrowed
)
VALUES (
    $member,
    $amount,
    0,
    $totalRepayable,
    $interest,
    'active',
    CURDATE()
)
");

// insert transaction
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
    $member,
    $amount,
    'OUT',
    CURDATE()
)
");

header("Location: loan_management.php");
exit();
