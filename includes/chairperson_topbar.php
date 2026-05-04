<?php

// safety check
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
?>
<header class="topbar">

    <div class="topbar-left">
        <h1 class="page-name">
            <?php echo isset($pageTitle) ? $pageTitle : 'Transaction Dashboard'; ?>
        </h1>

    </div>

    <div class="topbar-right">

        <a href="../chairperson/chairperson.php" class="switch-btn">
            Switch to Member View
        </a>

        <div class="profile-box">

            <div class="avatar">
                C
            </div>

            <div class="profile-info">
                <span class="name">Chairperson</span>
                <small><?php echo strtoupper($username); ?></small>
            </div>

        </div>

    </div>

</header>
