<?php

require_once '../../auth_check.php';

requireRole("Treasurer");

require_once '../../utils/config.php';

$conn = get_db();

// fetch members
$members = $conn->query("
SELECT * FROM members
ORDER BY firstname ASC
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $member = (int) $_POST['member_id'];
    $amount = (int) $_POST['amount'];

    // check if member already has savings row
    $check = $conn->query("
    SELECT * FROM savings
    WHERE member_id = $member
    ");

    if ($check->num_rows > 0) {

        // update savings
        $conn->query("
        UPDATE savings
        SET
            total_shares = total_shares + $amount,
            updated_at = CURDATE()
        WHERE member_id = $member
        ");

    } else {

        // create savings row
        $conn->query("
        INSERT INTO savings (
            member_id,
            total_shares,
            updated_at
        )
        VALUES (
            $member,
            $amount,
            CURDATE()
        )
        ");
    }

    // insert transaction
    $conn->query("
    INSERT INTO transactions (
        type,
        member_id,
        amount,
        direction,
        transaction_date
    )
    VALUES (
        'deposit',
        $member,
        $amount,
        'IN',
        CURDATE()
    )
    ");

    header("Location: transactions.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>New Deposit</title>

    <link rel="stylesheet" href="../../static/css/chairperson_layout.css">
    <link rel="stylesheet" href="../../static/css/chairperson_sidebar.css">
    <link rel="stylesheet" href="../../static/css/chairperson_topbar.css">

    <style>

        .box{
            width:400px;
            margin:40px auto;
            background:white;
            padding:25px;
            border-radius:10px;
        }

        input, select{
            width:100%;
            padding:12px;
            margin-top:12px;
        }

        button{
            margin-top:15px;
            padding:12px;
            width:100%;
        }

    </style>

</head>

<body>

<?php include("../../includes/treasurer_sidebar.php"); ?>

<div class="main">

    <div class="box">

        <h2>New Deposit</h2>

        <form method="POST">

            <select name="member_id" required>

                <?php while($m = $members->fetch_assoc()): ?>

                    <option value="<?php echo $m['member_id']; ?>">

                        <?php echo $m['firstname'] . " " . $m['lastname']; ?>

                    </option>

                <?php endwhile; ?>

            </select>

            <input
                type="number"
                name="amount"
                placeholder="Deposit Amount"
                required
            >

            <button type="submit">
                Save Deposit
            </button>

        </form>

    </div>

</div>

</body>

</html>
