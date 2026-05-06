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
            <a href="../treasurer/transactions.php"
                class="<?= ($currentPage == 'transactions.php') ? 'active' : ''; ?>">
                Dashboard
            </a>
        </li>

        <li>
            <a href="../treasurer/loan_management.php"
                class="<?= ($currentPage == 'loan_management.php') ? 'active' : ''; ?>">
                Loans
            </a>
        </li>

        
        
        <li class="manage-pwd">
            <a href="../treasurer/manage_password.php"
                class="<?= ($currentPage == 'manage_password.php') ? 'active' : ''; ?>">
                Manage password
            </a>
        </li>
        <li class="logout">
            <a href="../../auth/logout.php">
                Logout
            </a>
        </li>
</nav>