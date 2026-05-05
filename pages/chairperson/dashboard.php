<?php

require_once '../../auth_check.php';
requireRole('Chairperson');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit();
}

require_once '../../utils/config.php';
$conn = get_db();

$members = $conn->query("SELECT COUNT(*) as total FROM members")->fetch_assoc()['total'];
$active = $conn->query("SELECT COUNT(*) as total FROM members WHERE status='approved'")->fetch_assoc()['total'];
$inactive = $conn->query("SELECT COUNT(*) as total FROM members WHERE status='inactive'")->fetch_assoc()['total'];

$savings = $conn->query("SELECT SUM(total_shares) as total FROM savings")->fetch_assoc()['total'] ?? 0;
$loans = $conn->query("SELECT SUM(balance) as total FROM loans")->fetch_assoc()['total'] ?? 0;

$transactions = $conn->query("
    SELECT m.firstname, t.amount, t.direction, t.transaction_date
    FROM transactions t
    JOIN members m ON m.member_id = t.member_id
    ORDER BY t.transaction_date DESC
    LIMIT 5
");

$member_list = $conn->query("
    SELECT m.*, s.total_shares, l.balance
    FROM members m
    LEFT JOIN savings s ON m.member_id = s.member_id
    LEFT JOIN loans l ON m.member_id = l.member_id
");

$totalLoans = $conn->query("SELECT SUM(amount) total FROM loans")->fetch_assoc()['total'] ?? 0;
$totalCollected = $conn->query("SELECT SUM(total_paid) total FROM loans")->fetch_assoc()['total'] ?? 0;
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

    <?php include("../../includes/chairperson_sidebar.php"); ?>

    <?php $pageTitle = "Chairperson Dashboard";

    include("../../includes/chairperson_topbar.php"); ?>

    <div class="main">

        <!-- stats -->
        <div class="cards">
            <div class="card">
                <p>Total Members</p>
                <h2><?= $members ?></h2>
            </div>

            <div class="card">
                <p>Total Savings</p>
                <h2>K<?= number_format($savings) ?></h2>
            </div>

            <div class="card">
                <p>Total Loans</p>
                <h2>K<?= number_format($loans) ?></h2>
            </div>

            <div class="card">
                <p>Active Members</p>
                <h2><?= $active ?></h2>
            </div>
        </div>

        <div class="grid">

            <!-- recent transactions -->
            <div class="panel">
                <h3>Recent Transactions</h3>

                <div class="transaction-list">
                    <?php while ($t = $transactions->fetch_assoc()) { ?>
                        <div class="transaction">
                            <div>
                                <strong><?= $t['firstname'] ?></strong>
                                <span class="type"><?= $t['direction'] ?></span>
                            </div>

                            <div class="right">
                                <strong>K<?= number_format($t['amount']) ?></strong>
                                <span><?= $t['transaction_date'] ?></span>
                            </div>
                        </div>
                    <?php } ?>
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
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Savings</th>
                        <th>Loan</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($m = $member_list->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $m['firstname'] . " " . $m['lastname'] ?></td>
                            <td>K<?= number_format($m['total_shares'] ?? 0) ?></td>
                            <td>K<?= number_format($m['balance'] ?? 0) ?></td>
                            <td>
                                <span class="badge <?= strtolower($m['status']) ?>">
                                    <?= $m['status'] ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
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
                    data: [<?= $totalLoans ?>, <?= $totalCollected ?>],
                    backgroundColor: ['#e4990e', '#0b3d2e']
                }]
            }
        });
    </script>

</body>

</html>
