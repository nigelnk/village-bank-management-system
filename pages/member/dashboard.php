<?php 
require("../../utils/config.php");
$conn = get_db();
$conn->select_db("village_bank");

$member_id = 3; //gonna use $_SESSION["member_id"] when everything is linked i would assume

/* Queries */
$transactions_query = "SELECT type, amount, transaction_date FROM transactions WHERE member_id = $member_id";

$transactions = $conn->query($transactions_query);
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
                    Welcome, Yamiko <br>
                    <p id="member_id">Member id #<?php echo $member_id;?></p>
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
                    MWK 100,000
                </div>
                <div class="card">
                    <h6>Loan Balance</h5>
                    MWK 100,000
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
