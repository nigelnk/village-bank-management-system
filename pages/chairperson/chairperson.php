<?php
session_start();

require("../../utils/config.php");
$conn = get_db();
$conn->select_db("village_bank");

$member_id = $_SESSION["member_id"]; //We're gonna use $_SESSION["member_id"] when everything is linked i would assume

//Returns us to log in page if some how member id is not defined;
if (!isset($_SESSION["member_id"])) {
    $_SESSION["error_message"] = "Member not logged in properly.";
    header("Location: ../../auth/login.php");
    die();
}


/* Details query */
$stmt = $conn->prepare("SELECT 
             members.firstname,
             savings.total_shares AS total_savings,
             COALESCE(SUM(loans.amount), 0) AS total_active_loans
         FROM members
         LEFT JOIN savings  
             ON members.member_id = savings.member_id
         LEFT JOIN loans 
             ON members.member_id = loans.member_id
             AND loans.status = 'active'
         WHERE members.member_id = ?
        GROUP BY members.firstname, savings.total_shares;");
// The above query joins db tables: 'members', 'savings', 'loans' in order to get necessary details for the page i.e firstname, total savings and outstanding loan
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$details = $result->fetch_assoc();



/* Transactions query */
$transactions_query = "SELECT type, amount, transaction_date FROM transactions WHERE member_id = $member_id";
$transactions = $conn->query($transactions_query);

if (!$details) {
    $details = [
        "firstname" => "Unknown User",
        "total_savings" => 0,
        "total_active_loans" => 0
    ];
};

?>

<!DOCTYPE html>
<html>

<head>
    <title>Member page </title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../static/css/member_page.css">
</head>

<body>
    <div class="container">
        <header class="topbar">
            <div class="topbar-left">
                <div>
                    <img class="logo" src="../../static/photos/IMG-20260501-WA0108.jpg" alt="Logo" width="40px" height="40px">
                </div>
                <div class="welcome">
                    Welcome, <?php echo $details["firstname"]; ?> <br>
                    <p id="member_id">Member id #<?php echo $member_id ?></p>
                </div>
            </div>

            <div>
                <a href="../chairperson/dashboard.php" class="switch-btn">
                    Back to Dashboard
                </a>
                <a href="../../auth/login.php"><button class="logout">
                        Logout
                    </button></a>
            </div>
        </header>

        <main class="dashboard">
            <section class="reports-section">
                <div class="card">
                    <div class="card-icon">
                        <img src="../../static/photos/icons8-get-cash-50.png" alt="png">
                    </div>
                    <div class="card-info">
                        <h5>Total Savings</h5>
                        <span id="total-savings">MWK <?php echo number_format($details["total_savings"]); ?></span> <!-- number_format() adds commas (',') for i.e '60000' becomes '60,000'-->
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon">
                        <img src="../../static/photos/icons8-coin-50.png" alt="png">
                    </div>
                    <div class="card-info">
                        <h5>Loan Balance</h5>
                        <span id="loan-balance">MWK <?php echo number_format($details["total_active_loans"]); ?></span>
                    </div>
                </div>

            </section>

            <section class="transaction-history-section">
                <table>
                    <thead>
                        <tr>
                            <th>DATE</th>
                            <th>TYPE</th>
                            <th>AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = $transactions->fetch_assoc()) {
                            $date = $row["transaction_date"];
                            $type = $row["type"];
                            $amount = number_format($row["amount"]);

                            echo "<tr>";
                            echo "<td>$date</td>";
                            echo "<td><span class='transaction-type'>$type</span></td>";
                            echo "<td>K$amount</td>";
                            echo "<tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>

</html>

<?php
exit();
?>
