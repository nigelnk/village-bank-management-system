<?php
// =============================================
// connect to database
// =============================================
require_once __DIR__ . '/../../utils/config.php';
$conn = get_db();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Login check temporarily disabled
/*
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Chairperson') {
    header('Location: login.php');
    exit;
}
*/

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }
    $loan_id = (int)$_POST['loan_id'];
    if (isset($_POST['approve'])) {
        $stmt = $conn->prepare("UPDATE loans SET status = 'active' WHERE id = ? AND status = 'pending'");
        $stmt->bind_param("i", $loan_id);
        $stmt->execute();
    } elseif (isset($_POST['reject'])) {
        $stmt = $conn->prepare("UPDATE loans SET status = 'rejected' WHERE id = ? AND status = 'pending'");
        $stmt->bind_param("i", $loan_id);
        $stmt->execute();
    }
    header("Location: chairperson.php");
    exit;
}

$membersResult = $conn->query("SELECT COUNT(*) as total FROM members");
$members = $membersResult->fetch_assoc()['total'] ?? 0;

$pendingResult = $conn->query("SELECT COUNT(*) as total FROM loans WHERE status = 'pending'");
$pending = $pendingResult->fetch_assoc()['total'] ?? 0;

$savingsResult = $conn->query("SELECT SUM(total_shares) as total FROM savings");
$savings = $savingsResult->fetch_assoc()['total'] ?? 0;

$defaultersResult = $conn->query("SELECT COUNT(*) as total FROM members WHERE status = 'defaulter'");
$defaulters = $defaultersResult->fetch_assoc()['total'] ?? 0;

$loanRows = $conn->query("
    SELECT l.id, 
           CONCAT(m.firstname, ' ', m.lastname) AS member_name, 
           l.amount
    FROM loans l 
    JOIN members m ON l.member_id = m.member_id 
    WHERE l.status = 'pending'
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chairperson Dashboard</title>
     <link rel="stylesheet" href="../../static/css/chairperson.css">
    <script>
        function confirmAction(msg) {
            return confirm(msg);
        }
    </script>
</head>
<body>

<header>
    <div><strong>Village Bank</strong> - Chairperson Dashboard</div><br>
    <div>Welcome <?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?> | 
         <a style='color:#fff' href='logout.php'>Logout</a>
    </div>
</header>  
<hr class= "short-line">

<div class='wrap'>
    <nav class='side'>
        <a href='#'>Dashboard</a>
        <a href='#members'>Members</a>
        <a href='#loans'>Loans</a>
        <a href='#reports'>Reports</a>
    </nav>
    <main class='main'>
        <div class='cards'>
            <div class='card'><h3>Total Members</h3><p><?php echo (int)$members; ?></p></div>
            <div class='card'><h3>Pending Loans</h3><p><?php echo (int)$pending; ?></p></div>
            <div class='card'><h3>Total Savings</h3><p>MWK <?php echo number_format($savings); ?></p></div>
            <div class='card'><h3>Defaulters</h3><p><?php echo (int)$defaulters; ?></p></div>
        </div>

        <section id='loans'>
            <h2>Pending Loan Requests</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Member</th><th>Amount (MWK)</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($loanRows && $loanRows->num_rows > 0): ?>
                    <?php while ($row = $loanRows->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['member_name']); ?></td>
                            <td><?php echo number_format($row['amount']); ?></td>
                            <td>
                                <form method='POST' style='display:inline' onsubmit="return confirmAction('Approve this loan?')">
                                    <input type='hidden' name='loan_id' value='<?php echo $row['id']; ?>'>
                                    <input type='hidden' name='csrf_token' value='<?php echo $_SESSION['csrf_token']; ?>'>
                                    <button class='approve' name='approve'>Approve</button>
                                </form>
                                <form method='POST' style='display:inline' onsubmit="return confirmAction('Reject this loan?')">
                                    <input type='hidden' name='loan_id' value='<?php echo $row['id']; ?>'>
                                    <input type='hidden' name='csrf_token' value='<?php echo $_SESSION['csrf_token']; ?>'>
                                    <button class='reject' name='reject'>Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan='4' class='muted'>No pending loans</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>

</body>
</html>