<?php
session_start();

// Correct path to config (two levels up from pages/chairperson/)
require_once __DIR__ . '/../../utils/config.php';
$conn = get_db();
$conn->select_db("village_bank");

// Check login AFTER DB connection (order doesn't matter, but keep once)
if (!isset($_SESSION["member_id"])) {
    $_SESSION["error_message"] = "Member not logged in properly.";
    header("Location: ../../auth/login.php");  // up two levels to root/auth/
    exit();
}
$member_id = $_SESSION["member_id"];

// ---------- DYNAMIC STATS ----------
$totalMembers = $conn->query("SELECT COUNT(*) as total FROM members WHERE status='approved'")->fetch_assoc()['total'];
$totalSavings = $conn->query("SELECT COALESCE(SUM(amount),0) as total FROM transactions WHERE type='deposit'")->fetch_assoc()['total'];
$totalLoansOut = $conn->query("SELECT COALESCE(SUM(amount),0) as total FROM transactions WHERE type='loan' AND direction='OUT'")->fetch_assoc()['total'];
$totalLoansIn  = $conn->query("SELECT COALESCE(SUM(amount),0) as total FROM transactions WHERE type='loan' AND direction='IN'")->fetch_assoc()['total'];
$outstandingLoans = $totalLoansOut - $totalLoansIn;

// recent transactions (last 5)
$recent = $conn->query("
    SELECT t.*, m.firstname, m.lastname 
    FROM transactions t 
    JOIN members m ON m.member_id = t.member_id 
    ORDER BY t.transaction_date DESC 
    LIMIT 5
");

// members list for table
$membersList = $conn->query("
    SELECT m.*, COALESCE(s.total_shares,0) as savings, 
           (SELECT COALESCE(SUM(CASE WHEN direction='OUT' THEN amount ELSE 0 END),0) - 
                   COALESCE(SUM(CASE WHEN direction='IN' THEN amount ELSE 0 END),0)
            FROM transactions WHERE member_id = m.member_id AND type='loan') as loan_balance
    FROM members m
    LEFT JOIN savings s ON m.member_id = s.member_id
    WHERE m.status = 'approved'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chairperson Dashboard</title>
    <link rel="stylesheet" href="../../static/css/chairperson_layout.css">
    <link rel="stylesheet" href="../../static/css/chairperson_sidebar.css">
    <link rel="stylesheet" href="../../static/css/chairperson_topbar.css">
    <link rel="stylesheet" href="../../static/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- Sidebar and Topbar: correct relative paths from pages/chairperson/ -->
<?php include __DIR__ . '/../../includes/chairperson_sidebar.php'; ?>
<?php $pageTitle = "Chairperson Dashboard"; include __DIR__ . '/../../includes/chairperson_topbar.php'; ?>

<div class="main">
    <!-- stats cards -->
    <div class="cards">
        <div class="card"><p>Total Members</p><h2><?php echo $totalMembers; ?></h2></div>
        <div class="card"><p>Total Savings</p><h2>K<?php echo number_format($totalSavings); ?></h2></div>
        <div class="card"><p>Total Loans (disbursed)</p><h2>K<?php echo number_format($totalLoansOut); ?></h2></div>
        <div class="card"><p>Outstanding Loans</p><h2>K<?php echo number_format($outstandingLoans); ?></h2></div>
    </div>

    <div class="grid">
        <!-- recent transactions -->
        <div class="panel">
            <h3>Recent Transactions</h3>
            <div class="transaction-list">
                <?php while($tx = $recent->fetch_assoc()): ?>
                <div class="transaction">
                    <div>
                        <strong><?php echo $tx['firstname'].' '.$tx['lastname']; ?></strong>
                        <span class="type"><?php echo $tx['direction']; ?></span>
                    </div>
                    <div class="right">
                        <strong>K<?php echo number_format($tx['amount']); ?></strong>
                        <span><?php echo $tx['transaction_date']; ?></span>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- chart -->
        <div class="panel center">
            <h3>Loan Overview</h3>
            <canvas id="loanChart"></canvas>
        </div>
    </div>

    <!-- members table -->
    <div class="panel">
        <h3>Members</h3>
        <table>
            <thead><tr><th>Name</th><th>Savings</th><th>Loan Balance</th><th>Status</th></tr></thead>
            <tbody>
                <?php while($m = $membersList->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $m['firstname'].' '.$m['lastname']; ?></td>
                    <td>K<?php echo number_format($m['savings']); ?></td>
                    <td>K<?php echo number_format($m['loan_balance']); ?></td>
                    <td><span class="badge approved"><?php echo $m['status']; ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const ctx = document.getElementById('loanChart');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Disbursed', 'Collected'],
            datasets: [{
                data: [<?php echo $totalLoansOut; ?>, <?php echo $totalLoansIn; ?>],
                backgroundColor: ['#e4990e', '#0b3d2e']
            }]
        }
    });
</script>
</body>
</html>