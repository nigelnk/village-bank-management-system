<?php
require_once '../../auth_check.php';
requireRole(['Chairperson', 'Member']);

require_once "../../utils/config.php";
$conn = get_db();

$member_id = $_SESSION["member_id"]; //We're gonna use $_SESSION["member_id"] when everything is linked i would assume

//Returns us to log in page if some how member id is not defined;
if (!isset($_SESSION["user_id"])) {
    $_SESSION["error_message"] = "Login failed! Please try again.";
    header("Location: ../../auth/login.php");
    die();
}


/* Details query */
$stmt = $conn->prepare("SELECT firstname, lastname FROM members WHERE member_id=?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$details = $result->fetch_assoc();



/* Transactions query */
$transactions_query = "SELECT type, amount, transaction_date FROM transactions WHERE member_id = '$member_id'";
$transactions = $conn->query($transactions_query);

if (!$details) {
    $details = [
        "firstname" => "Unknown User",
        "lastname" => "Unknown lastname",
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
                    Welcome, <?php echo $details["firstname"] . " " . $details["lastname"]; ?> <br>
                </div>
            </div>

            <div>
                <a href="manage_password.php"><button class="logout">
                        Manage Password
                    </button></a>
                <a href="../../auth/login.php"><button class="logout">
                        Logout
                    </button></a>
            </div>
        </header>

        <main class="dashboard">
            <?php
            include("../../includes/password.php");
            ?>
        </main>
    </div>
</body>

</html>

<?php
exit();
?>