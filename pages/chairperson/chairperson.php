<?php
// =============================================
// connect to database dont know which method is being used
// =============================================
require_once __DIR__ . '/config/config.php';   // always works, even if moved
$conn = get_db();   // returns MySQLi connection to 'village_bank'


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//turned off log in functionality since there is no page yet

/* Verify user is logged in and is Chairperson (exact case from roles table)
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Chairperson') {
    header('Location: login.php');
    exit;
}*/


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $loan_id = (int)$_POST['loan_id'];  // ensure integer

    if (isset($_POST['approve'])) {
        // Update loan status to 'active' (or your chosen approved status)
        $stmt = $conn->prepare("UPDATE loans SET status = 'active' WHERE id = ? AND status = 'pending'");
        $stmt->bind_param("i", $loan_id);
        $stmt->execute();
        // Optionally insert a transaction record for loan disbursement
    } 
    elseif (isset($_POST['reject'])) {
        // Update loan status to 'rejected'
        $stmt = $conn->prepare("UPDATE loans SET status = 'rejected' WHERE id = ? AND status = 'pending'");
        $stmt->bind_param("i", $loan_id);
        $stmt->execute();
    }

    // Redirect to avoid form resubmission
    header("Location: chairperson.php");
    exit;
}


//  Dashboard statistics (using correct column names)

$membersResult = $conn->query("SELECT COUNT(*) as total FROM members");
$members = $membersResult->fetch_assoc()['total'] ?? 0;

// Pending loans (status = 'pending')
$pendingResult = $conn->query("SELECT COUNT(*) as total FROM loans WHERE status = 'pending'");
$pending = $pendingResult->fetch_assoc()['total'] ?? 0;

// Total savings = sum of total_shares from savings table
$savingsResult = $conn->query("SELECT SUM(total_shares) as total FROM savings");
$savings = $savingsResult->fetch_assoc()['total'] ?? 0;

// Defaulters (members with status = 'defaulter')
$defaultersResult = $conn->query("SELECT COUNT(*) as total FROM members WHERE status = 'defaulter'");
$defaulters = $defaultersResult->fetch_assoc()['total'] ?? 0;

// Pending loan details – join members to get full name
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
    <style>
        body { font-family: Arial, sans-serif; margin:0; }
        header { background:#2c3e50; color:white; padding:15px; display:flex; justify-content:space-between; }
        .wrap { display:flex; }
        .side { width:200px; background:#34495e; padding:15px; }
        .side a { display:block; color:white; padding:10px; text-decoration:none; }
        .main { flex:1; padding:20px; }
        .cards { display:flex; gap:20px; margin-bottom:30px; flex-wrap:wrap; }
        .card { background:#ecf0f1; padding:15px; border-radius:5px; width:calc(25% - 20px); min-width:150px; }
        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid #ddd; padding:8px; text-align:left; }
        .approve { background:green; color:white; border:none; padding:5px 10px; cursor:pointer; }
        .reject { background:red; color:white; border:none; padding:5px 10px; cursor:pointer; }
        .muted { color:#7f8c8d; text-align:center; }
    </style>
    <script>
        function confirmAction(msg) {
            return confirm(msg);
        }
    </script>
<header>
    <div><strong>Village Bank</strong> - Chairperson Dashboard</div>
    <div>Welcome <?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?> | 
         <a style='color:#fff' href='logout.php'>Logout</a>
    </div>
</header>>
<div class='wrap'>
    <nav class='side'>
        <a href='#'>Dashboard</a>
        <a href='#members'>Members</a>
        <a href='#loans'>Loans</a>
        <a href='#reports'>Reports</a>
    </nav>
    <main class='main'>
        <!-- Statistics Cards -->
        <div class='cards'>
            <div class='card'><h3>Total Members</h3><p><?php echo (int)$members; ?></p></div>
            <div class='card'><h3>Pending Loans</h3><p><?php echo (int)$pending; ?></p></div>
            <div class='card'><h3>Total Savings</h3><p>MWK <?php echo number_format($savings); ?></p></div>
            <div class='card'><h3>Defaulters</h3><p><?php echo (int)$defaulters; ?></p></div>
        </div>

        <!-- Pending Loans Table -->
        <section id='loans'>
            <h2>Pending Loan Requests</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Member</th>
                        <th>Amount (MWK)</th>
                        <th>Action</th>
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