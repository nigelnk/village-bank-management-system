<?php 
require("../../utils/config.php");
$conn = get_db();
$conn->select_db("village_bank");

$member_id = 3; //We're gonna use $_SESSION["member_id"] when everything is linked i would assume

/* Queries */
$transactions_query = "SELECT type, amount, transaction_date FROM transactions WHERE member_id = $member_id";
$details_query = "SELECT 
                      members.firstname,
                      savings.total_shares AS total_savings,
                      SUM(loans.amount) AS total_active_loans
                  FROM members
                  JOIN savings 
                      ON members.member_id = savings.member_id
                  JOIN loans 
                      ON members.member_id = loans.member_id
                  WHERE members.member_id = $member_id
                    AND loans.status = 'active'
                 GROUP BY members.firstname, savings.total_shares;";
// The above query joins db tables: 'members', 'savings', 'loans' in order to get necessary details for the page i.e firstname, total savings and outstanding loan


/* Query results */
$transactions = $conn->query($transactions_query);
$query_results = $conn->query($details_query);
$details = $query_results->fetch_assoc();

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
            <div>
                <div class="welcome">
                    Welcome, <?php echo $details["firstname"];?> <br>
                    <p id="member_id">Member id #<?php echo $member_id?></p>
                </div>
            </div>

            <div>
                <span class="notifications">
                    
                </span>
                
                <button class="logout">
                    Logout
                </button>
            </div>
        </header>

        <main class="dashboard">
            <section class="reports-section">
                <div class="card">
                    <h5>Total Savings</h5>
                    MWK <?php echo $details["total_savings"];?>
                </div>
                <div class="card">
                    <h6>Loan Balance</h5>
                    MWK <?php echo $details["total_active_loans"];?>
                </div>
                <div class="card">
                    <h5>Available credit</h5>
                    MWK 100,000
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
                            $amount = $row["amount"];
                        
                            echo "<tr>";
                            echo "<td>$date</td>";
                            echo "<td>$type</td>";
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
