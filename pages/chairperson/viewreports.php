<?php
session_start();

if (!isset($_SESSION["member_id"])) {
    $_SESSION["error_message"] = "Member not logged in properly.";
    header("Location: ../../auth/login.php");
    exit();
}
$member_id = $_SESSION["member_id"];

require_once __DIR__ . '/../../utils/config.php';
$conn = get_db();
$conn->select_db("village_bank");

// Stats queries (only these remain)
$totalSavings = $conn->query("SELECT COALESCE(SUM(amount),0) as total FROM transactions WHERE type='deposit'")->fetch_assoc()['total'];
$loanQuery = "SELECT COALESCE(SUM(CASE WHEN type='loan' AND direction='OUT' THEN amount ELSE 0 END),0) -
                     COALESCE(SUM(CASE WHEN type='loan' AND direction='IN' THEN amount ELSE 0 END),0) as outstanding FROM transactions";
$outstandingLoans = $conn->query($loanQuery)->fetch_assoc()['outstanding'];
$monthlyDeposits = $conn->query("SELECT COALESCE(SUM(amount),0) as month_deposits 
                                 FROM transactions WHERE type='deposit' 
                                 AND MONTH(transaction_date)=MONTH(CURDATE()) 
                                 AND YEAR(transaction_date)=YEAR(CURDATE())")->fetch_assoc()['month_deposits'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transaction Reports</title>
    <link rel="stylesheet" href="../../static/css/chairperson_layout.css">
    <link rel="stylesheet" href="../../static/css/chairperson_sidebar.css">
    <link rel="stylesheet" href="../../static/css/chairperson_topbar.css">
    <link rel="stylesheet" href="../../static/css/transaction.css">
</head>
<body>

    <?php include __DIR__ . '/../../includes/chairperson_sidebar.php'; ?>
    <?php $pageTitle = "Transaction Reports"; include __DIR__ . '/../../includes/chairperson_topbar.php'; ?>

    <div class="main">
        <!-- stats cards only -->
        <div class="cards">
            <div class="card money-card">
                <h3>Total Member Savings</h3>
                <h2>MK<?php echo number_format($totalSavings); ?></h2>
                <div class="gold-line"></div>
            </div>
            <div class="card">
                <h3>Outstanding Loans</h3>
                <h2>MK<?php echo number_format($outstandingLoans); ?></h2>
                <div class="gold-line"></div>
            </div>
            <div class="card">
                <h3>Deposits This Month</h3>
                <h2>MK<?php echo number_format($monthlyDeposits); ?></h2>
                <div class="gold-line"></div>
            </div>
        </div>
    </div>

</body>
</html>