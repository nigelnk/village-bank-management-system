<?php

require_once '../../auth_check.php';

requireRole('Treasurer');

require_once '../../utils/config.php';
$conn = get_db();

// stat cards

// 1. Total member savings
$savingsQuery = "SELECT COALESCE(SUM(total_shares), 0) AS total_savings 
                 FROM savings";

$totalSavings = $conn->query($savingsQuery)
                     ->fetch_assoc()['total_savings'];


// 2. Outstanding loans
$loanQuery = "SELECT COALESCE(SUM(balance), 0) AS outstanding_loans
              FROM loans
              WHERE status = 'active'";

$outstandingLoans = $conn->query($loanQuery)
                         ->fetch_assoc()['outstanding_loans'];


// 3. Available balance (money in - money out)
$balanceQuery = "SELECT 
                    COALESCE(SUM(CASE WHEN direction = 'IN' THEN amount ELSE 0 END), 0)
                    -
                    COALESCE(SUM(CASE WHEN direction = 'OUT' THEN amount ELSE 0 END), 0)
                    AS available_balance
                 FROM transactions";

$availableBalance = $conn->query($balanceQuery)
                         ->fetch_assoc()['available_balance'];


// Search & pagination
$limit = 10;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// search query
$where = '';
if ($search !== '') {
    $where = "WHERE members.firstname LIKE '%$search%' 
              OR members.lastname LIKE '%$search%' 
              OR transactions.type LIKE '%$search%'";
}

// pagination
$countQuery = "SELECT COUNT(*) as total 
               FROM transactions 
               JOIN members 
               ON members.member_id = transactions.member_id 
               $where";

$totalRows = $conn->query($countQuery)->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// fetch transactions
$sql = "SELECT transactions.*, members.firstname, members.lastname 
        FROM transactions 
        JOIN members 
        ON members.member_id = transactions.member_id 
        $where
        ORDER BY transaction_date DESC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

$balances = [];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Transactions Page</title>

    <link rel="stylesheet" href="../../static/css/chairperson_layout.css">
    <link rel="stylesheet" href="../../static/css/chairperson_sidebar.css">
    <link rel="stylesheet" href="../../static/css/chairperson_topbar.css">
    <link rel="stylesheet" href="../../static/css/transaction.css">
</head>

<body>

    <!-- Sidebar -->
    <?php include("../../includes/treasurer_sidebar.php"); ?>

    <!-- Topbar -->
    <?php
    $pageTitle = "Treasurer Dashboard";
    include("../../includes/treasurer_topbar.php");
    ?>

    <div class="main">

        <!-- stats -->
        <div class="cards">

            <div class="card money-card">
                <h3>Total Member Savings</h3>
                <h2>MK<?php echo number_format($totalSavings); ?></h2>
                <div class="gold-line"></div>
            </div>

            <div class="card">
                <h3 style="color: black;">Outstanding Loans</h3>
                <h2 style="color: black;">MK<?php echo number_format($outstandingLoans); ?></h2>
                <div class="gold-line"></div>
            </div>

            <div class="card">
                <h3 style="color: black;">Available Balance</h3>
                <h2 style="color: black;">MK<?php echo number_format($availableBalance); ?></h2>
                <div class="gold-line"></div>
            </div>

        </div>

        <!-- search -->
        <div class="search-bar">
            <form method="get">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search by name or type"
                    value="<?= htmlspecialchars($search) ?>"
                >

                <button type="submit">Search</button>
            </form>
        </div>

        <div class="cashbook-card">
            <h3>Cashbook Actions</h3>

            <div class="actions">
                <a href="cashbook.php?action=generate" class="btn cream">
                    Generate Cashbook
                </a>

                <a href="cashbook.php?action=send" class="btn gold">
                    Send to chairperson
                </a>

                <a href="new_transaction.php" class="btn green">
                    New Transaction
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="table-box">

            <table>

                <tr>
                    <th>Member</th>
                    <th>Type</th>
                    <th>Direction</th>
                    <th>Amount</th>
                    <th>Savings Balance</th>
                    <th>Date</th>
                </tr>

                <?php while ($row = $result->fetch_assoc()) :

                    $member = $row['member_id'];

                    if (!isset($balances[$member])) {
                        $balances[$member] = 0;
                    }

                    if ($row['type'] == "deposit") {
                        $balances[$member] += $row['amount'];
                    }

                ?>

                    <tr>

                        <td>
                            <?php echo $row['firstname'] . " " . $row['lastname']; ?>
                        </td>

                        <td>
                            <?php if ($row['type'] == "loan") : ?>

                                <span class='loan-badge'>Loan</span>

                            <?php else : ?>

                                <span class='deposit-badge'>Deposit</span>

                            <?php endif; ?>
                        </td>

                        <td class="<?php echo strtolower($row['direction']); ?>">
                            <?php echo $row['direction']; ?>
                        </td>

                        <td class="amount">
                            MK <?php echo number_format($row['amount']); ?>
                        </td>

                        <td class="amount">
                            MK <?php echo number_format($balances[$member]); ?>
                        </td>

                        <td>
                            <?php echo $row['transaction_date']; ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            </table>

            <!-- pagination -->
            <div class="pagination">

                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">
                        Prev
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                    <a 
                        href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                        class="<?= ($i == $page) ? 'active' : '' ?>"
                    >
                        <?= $i ?>
                    </a>

                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">
                        Next
                    </a>
                <?php endif; ?>

            </div>

        </div>

    </div>

</body>

</html>
