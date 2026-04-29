<?php
//Summary cards queries
include("db_setup.php");

$savingsQuery="SELECT COALESCE(SUM(AMOUNT), 0) as total_savings FROM transactions WHERE TYPE='deposit' ";
$savingsResult = $conn -> query( $savingsQuery);
$totalSavings = $savingsResult -> fetch_assoc()['total_savings'];

//outstanding loans =   loans issued(out) - repayments(in)
$loanQuery = "SELECT COALESCE(SUM (CASE WHEN TYPE='loan' AND DIRECTION='OUT' THEN amount ELSE 0 END), 0) 
-
SELECT COALESCE(SUM (CASE WHEN TYPE='loan' AND DIRECTION='IN' THEN amount ELSE 0 END), 0 )
AS outstanding_loans FROM transactions";

$loanResult = conn -> query($loanQuery);
$outstandingLoans = $loanResult -> fetch_assoc()['outstanding_loans'];

//deposits for the month
$monthQuery = "SELECT COALESCE(SUM(amount), 0) AS month_deposits FROM transactions WHERE type='deposit' AND MONTH(transaction_date) = MONTH(CURDATE()) AND YEAR(transaction_date) = YEAR(CURDATE())";
$monthResult = $conn -> query($monthQuery);
$monthlyDeposits = $monthResult -> fetch_assoc()['monthlyDeposits'];

//fetching transactions
$sql = "SELECT * FROM transactions ORDER BY transaction_date DESC";
$result = $conn -> query($sql);

$balances=[];

?>



<!DOCTYPE html>
<html>
    <head> 
        <title> Transactions Page</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
       
<style>  
 *{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial,sans-serif;
}

body{
display:flex;
background:#F7F2E7;
color:#233126;
}


/* sidebar*/ 

.sidebar{
width:260px;
height:100vh;
background:#1F4D36;
color:white;
padding:30px 22px;
position:fixed;
left:0;
top:0;
}

.logo{
font-size:24px;
font-weight:bold;
margin-bottom:30px;
color:#C9A227;
}

.sidebar hr{
border:none;
height:1px;
background:rgba(255,255,255,.2);
margin:20px 0;
}

.sidebar ul{
list-style:none;
}

.sidebar li{
margin:18px 0;
}

.sidebar a{
text-decoration:none;
color:#f4f4f4;
font-size:17px;
display:block;
padding:12px 14px;
border-radius:10px;
transition:.2s;
}

.sidebar a:hover{
background:#2E6B4A;
}

.add-member-btn{
margin-top:30px;
display:block;
background:#C9A227;
color:#173423;
padding:14px;
text-align:center;
text-decoration:none;
font-weight:bold;
border-radius:12px;
}



/* main gist*/

.main{
margin-left:260px;
width:calc(100% - 260px);
padding:40px;
}

.page-title{
margin-bottom:30px;
}

.page-title h1{
font-size:34px;
color:#1F4D36;
}


/* cards*/


.cards{
display:grid;
grid-template-columns:2fr 1fr 1fr;
gap:25px;
margin-bottom:35px;
}

.card{
background:white;
padding:28px;
border-radius:18px;
box-shadow:0 4px 12px rgba(0,0,0,.08);
}

.money-card{
background:
linear-gradient(
135deg,
#1F4D36,
#2D6A4F
);
color:white;
}

.money-card h3{
opacity:.9;
}

.money-card h2{
font-size:42px;
margin-top:18px;
}

.card h3{
color:#666;
margin-bottom:14px;
}

.card h2{
font-size:30px;
color:#1F4D36;
}

.gold-line{
height:5px;
width:70px;
background:#C9A227;
margin-top:16px;
border-radius:8px;
}



/* table*/

.table-box{
background:white;
border-radius:18px;
overflow:hidden;
box-shadow:0 4px 12px rgba(0,0,0,.08);
}

table{
width:100%;
border-collapse:collapse;
}

th{
background:#1F4D36;
color:white;
padding:18px;
text-align:left;
}

td{
padding:16px;
border-bottom:1px solid #ececec;
}

tr:hover{
background:#fffdf8;
}

.loan-badge{
background:#ffe7c4;
color:#7c5200;
padding:6px 11px;
border-radius:8px;
font-weight:bold;
}

.deposit-badge{
background:#d8f3dc;
color:#245b38;
padding:6px 11px;
border-radius:8px;
font-weight:bold;
}

.out{
color:#b21f1f;
font-weight:bold;
}

.in{
color:#1c7d38;
font-weight:bold;
}

.amount{
font-weight:bold;
}

</style>
</head>
<body>
    
    <!--sidebar-->
    <nav class="sidebar">
        <div class="logo">
            <img src="" alt="VB-Logo">
        </div>
        <hr>
        <ul>
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">Members</a></li>
            <li><a href="#">Loans</a></li> 
            <li><a href="#">Transactions</a></li>
            <li><a href="#">View Reports</a><li>
            <li><a href="#">Profile</a></li>
        </ul>
         <a href="" id="add-member-btn">+ Add Member</a>

</nav>    

<div class="main">
    <div class="page-title"> <h1>Transactions </h1></div>

    //cards
    <div class="cards">
        <div class="card money-card">
            <h3>Total member savings</h3>
            <h2>MK<?php echo number_format($total_savings); ?></h2> 
            <div class="gold-line"></div>
        </div>
        <div class="card">
            <h3> Outstanding Loans</h3>
            <h2><?php echo number_format($outstandingLoans);?></h2>
            <div class="gold-line"></div>
        </div>
        <div class="card">
            <h3> Deposits this month</h3>
            <h2><?php echo number_format($monthlyDeposits);?>
            <div class="gold-line"></div> 
        </div>
    </div>
</div>

<!--transactions table-->
<div class="table-box">
    <table>
        <tr>
            <th>Member ID</th>
            <th>Type</th>
            <th>Direction</th>
            <th>Amount</th>
            <th>Savings Balanve</th>
            <th>Date</th>
        </tr>
<?php
 while($row=$result->fetch_assoc()){

$member=$row['member_id'];

if(!isset($balances[$member])){
$balances[$member]=0;
}

if($row['type']=="deposit"){
$balances[$member]+=$row['amount'];
}?>

<tr>
<td>
<?php echo $row['member_id']; ?>
</td>

<td>

<?php
if($row['type']=="loan"){
echo "<span class='loan-badge'>Loan</span>";
}
else{
echo "<span class='deposit-badge'>Deposit</span>";
}
?>

</td>

<td class="<?php echo strtolower($row['direction']); ?>">
<?php echo $row['direction']; ?>
</td>

<td class="amount">
MK <?php echo number_format($row['amount']); ?>
</td>

<td>
MK <?php echo number_format($balances[$member]); ?>
</td>

<td>
<?php echo $row['transaction_date']; ?>
</td>

</tr>

    </table>
</div>
</body>
</html>