<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="sidebar">

    <div class="logo">
        <img src="../../static/photos/logo.jpeg" class="logo" alt="VB Logo">
    </div>

    <hr>

    <ul>
        <li>
            <a href="../chairperson/dashboard.php"
               class="<?= ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
               Dashboard
            </a>
        </li>

        <li>
            <a href="member_details.php"
               class="<?= ($currentPage == 'member_details.php') ? 'active' : ''; ?>">
               Members
            </a>
        </li>

        <li>
            <a href="transactions.php"
               class="<?= ($currentPage == 'transactions.php') ? 'active' : ''; ?>">
               Transactions
            </a>
        </li>

        <li>
            <a href="reports.php"
               class="<?= ($currentPage == 'reports.php') ? 'active' : ''; ?>">
               View Reports
            </a>
        </li>
</nav>