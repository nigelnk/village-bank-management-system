<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="sidebar">

    <div class="logo">
        <img src="../assets/images/logo.png" alt="VB Logo">
    </div>

    <hr>

    <ul>
        <li>
            <a href="dashboard.php"
               class="<?= ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
               Dashboard
            </a>
        </li>

        <li>
            <a href="members.php"
               class="<?= ($currentPage == 'members.php') ? 'active' : ''; ?>">
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

        <li>
            <a href="profile.php"
               class="<?= ($currentPage == 'profile.php') ? 'active' : ''; ?>">
               Profile
            </a>
        </li>
    </ul>

    <a href="#" class="add-member-btn"> + Add Memebr </a>
</nav>