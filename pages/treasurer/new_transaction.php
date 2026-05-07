<?php
require_once '../../utils/config.php';
$conn = get_db();


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $member_id = $_POST['member_id'];
    $type = $_POST['type'];
    $direction = $_POST['direction'];
    $amount = $_POST['amount'];

    // automatic safety rule:
    // shares can ONLY be incoming
    if ($type == "share") {
        $direction = "in";
    }

    $transaction_date = date("Y-m-d");

    // INSERT TRANSACTION
    $stmt = $conn->prepare("
        INSERT INTO transactions
        (
            type,
            member_id,
            amount,
            direction,
            transaction_date
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sisss",
        $type,
        $member_id,
        $amount,
        $direction,
        $transaction_date
    );

    $stmt->execute();

    // return to dashboard
    header("Location: dashboard.php");
    exit();
}



$members = $conn->query("
    SELECT member_id, firstname, lastname
    FROM members
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>New Transaction</title>
    <style>

        body{
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 30px;
        }

        .form-card{
            background: white;
            width: 420px;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        h2{
            margin-bottom: 20px;
            color: #0b3d2e;
        }

        label{
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }

        select,
        input{
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        button{
            background: #0b3d2e;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
        }

        button:hover{
            opacity: 0.92;
        }

    </style>

</head>

<body>

<div class="form-card">
    <h2>New Transaction</h2>
    <form method="POST">
        <label>Member</label>
        <select name="member_id" required>
            <option value="">Select Member</option>
            <?php while($member = $members->fetch_assoc()): ?>
                <option value="<?php echo $member['member_id']; ?>">
                    <?php
                        echo $member['first_name']
                        . ' ' .
                        $member['last_name'];
                    ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Transaction Type</label>
        <select name="type" id="type" required>
            <option value="share">Share</option>
            <option value="loan">Loan</option>
        </select>
        <label>Direction</label>
        <select name="direction" id="direction" required>
            <option value="in">In</option>
            <option value="out">Out</option>
        </select>

        <label>Amount</label>
        <input type="number" name="amount"  required >

        <button type="submit">Save Transaction</button>

    </form>
</div>

<script>

    const type = document.getElementById("type");

    const direction = document.getElementById("direction");

    function updateDirection() {

        if(type.value === "share") {

            direction.value = "in";

            direction.disabled = true;

        } else {

            direction.disabled = false;

        }

    }

    // run immediately on page load
    updateDirection();

    // run whenever type changes
    type.addEventListener("change", updateDirection);

</script>

</body>
</html>
