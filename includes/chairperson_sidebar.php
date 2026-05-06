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
            <a href="reports.php"
                class="<?= ($currentPage == 'reports.php') ? 'active' : ''; ?>">
                Reports
            </a>
        </li>

        <li>
            <a href="../chairperson/member_approval.php"
                class="<?= ($currentPage == 'member_approval.php') ? 'active' : ''; ?>">
                Approve members
            </a>
        </li>
        
        <li class="manage-pwd">
            <a href="../chairperson/manage_password.php"
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
