<?php
require_once 'auth_check.php';

require_once '../../utils/config.php';
$conn = get_db();

// paginate for every 10 members in the loans tble for better viewing
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// search
$search = $_GET['search'] ?? "";


$totalLoans = $conn->query("SELECT SUM(amount) total FROM loans")->fetch_assoc()['total'] ?? 0;
$totalCollected = $conn->query("SELECT SUM(total_paid) total FROM loans")->fetch_assoc()['total'] ?? 0;
$totalBorrowers = $conn->query("SELECT COUNT(DISTINCT member_id) total FROM loans")->fetch_assoc()['total'] ?? 0;

// fetch members with loans
$query = "
SELECT loans.*, members.firstname, members.lastname
FROM loans
JOIN members ON loans.member_id = members.member_id
WHERE members.firstname LIKE '%$search%'
   OR members.lastname LIKE '%$search%'
ORDER BY loans.id DESC
LIMIT $limit OFFSET $offset
";

$result = $conn->query($query);

$totalRows = $conn->query("
SELECT COUNT(*) total
FROM loans
JOIN members ON loans.member_id = members.member_id
WHERE members.firstname LIKE '%$search%'
   OR members.lastname LIKE '%$search%'
")->fetch_assoc()['total'];

$totalPages = ceil($totalRows / $limit);

// fetch members for member dropdown in give loan modal
$members = $conn->query("SELECT * FROM members");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Loan Dashboard</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../static/css/loan_management_style.css">

</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>Village Bank</h2>
        <a href="#">Dashboard</a>
        <a href="#">Members</a>
        <a href="#">Loans</a>
    </div>

    <!-- MAIN -->
    <div class="main">

        <div class="topbar">
            <h1>Loan Dashboard</h1>
            <button class="btn-orange" onclick="openLoanModal()">+ Give Loan</button>
        </div>

        <!-- CARDS -->
        <div class="cards">

            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <h3>Total Loans</h3>
                        <p>K<?php echo number_format($totalLoans); ?></p>
                    </div>

                    <img src="../../static/photos/money.png" width="42">
                </div>
            </div>


            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <h3>Total Collected</h3>
                        <p>K<?php echo number_format($totalCollected); ?></p>
                    </div>

                    <img src="../../static/photos/chart.png" width="42">
                </div>
            </div>

            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <h3>Borrowers</h3>
                        <p><?php echo $totalBorrowers; ?></p>
                    </div>

                    <img src="../../static/photos/users.png" width="42">
                </div>
            </div>

        </div>

        <!-- pie chart -->
        <div class="box" style="text-align:center;">
            <div style="width:280px;margin:auto;">
                <canvas id="loanChart"></canvas>
            </div>
        </div>

        <!-- table -->
        <div class="box">

            <form method="GET" class="search">
                <input type="text" name="search"
                    placeholder="Search member..."
                    value="<?php echo $search; ?>">
                <button class="btn-green">Search</button>
            </form>

            <div class="table-wrap">
                <table>

                    <tr>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Interest</th>
                        <th>Date Borrowed</th>
                        <th>Date Paid</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>
                            <td><?php echo $row['firstname'] . " " . $row['lastname']; ?></td>

                            <td>K<?php echo number_format($row['amount']); ?></td>

                            <td>K<?php echo number_format($row['total_paid']); ?></td>

                            <td>K<?php echo number_format($row['balance']); ?></td>

                            <td><?php echo $row['interest']; ?>%</td>

                            <td><?php echo $row['date_borrowed']; ?></td>

                            <td>
                                <?php
                                if ($row['date_paid'] == NULL) {
                                    echo "-";
                                } else {
                                    echo $row['date_paid'];
                                }
                                ?>
                            </td>

                            <td>
                                <?php
                                if ($row['balance'] <= 0) {
                                    echo "<span class='badge-paid'>PAID</span>";
                                } else {
                                    echo "<span class='badge-unpaid'>UNPAID</span>";
                                }
                                ?>
                            </td>

                            <td>
                                <button class="btn-green btn-small"
                                    onclick="openManage(
                                        <?php echo $row['id']; ?>,
                                        '<?php echo $row['firstname'] . " " . $row['lastname']; ?>',
                                        <?php echo $row['interest']; ?>
                                    )">
                                    Manage
                                </button>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                </table>
            </div>

            <!-- paginationn -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>">
                        <button class="btn-small btn-green"><?php echo $i; ?></button>
                    </a>
                <?php endfor; ?>
            </div>

        </div>

    </div>

    <!-- this will open a modal where will be inputing loan paid amounts -->
    <div class="modal" id="manageModal">
        <div class="modal-content">

            <h3 id="memberName"></h3>

            <form method="POST" action="update_loan.php">

                <input type="hidden" name="loan_id" id="loan_id">

                <input type="number" name="amount_paid" placeholder="Amount Paid" required>

                Interest: <input type="number" name="interest" id="interest" placeholder="Interest">

                <div class="actions">
                    <button class="btn-green">Save</button>
                    <button type="button" class="btn-orange" onclick="closeModal()">Close</button>
                </div>

            </form>

        </div>
    </div>

    <!-- give loan modal, will be opened once we click give loan button -->
    <div class="modal" id="loanModal">
        <div class="modal-content">

            <h3>Give Loan</h3>

            <form method="POST" action="give_loan.php">

                <select name="member_id">
                    <?php while ($m = $members->fetch_assoc()): ?>
                        <option value="<?php echo $m['member_id']; ?>">
                            <?php echo $m['firstname'] . " " . $m['lastname']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <input type="number" name="amount" placeholder="Amount">
                <input type="number" name="interest" placeholder="Interest">
                <input type="date" name="return_date">

                <div class="actions">
                    <button class="btn-green">Save</button>
                    <button type="button" class="btn-orange" onclick="closeLoanModal()">Close</button>
                </div>

            </form>

        </div>
    </div>

    <script>
        const ct = document.getElementById('loanChart');

        new Chart(ct, {
            type: 'pie',
            data: {
                labels: ['Disbursed', 'Collected'],
                datasets: [{
                    data: [
                        <?php echo $totalLoans; ?>,
                        <?php echo $totalCollected; ?>
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        /* modals */
        
        function openManage(id, name, interest) {
            document.getElementById('manageModal').style.display = 'block';
            document.getElementById('loan_id').value = id;
            document.getElementById('memberName').innerText = name;
            document.getElementById('interest').value = interest;
        }

        function closeModal() {
            document.getElementById('manageModal').style.display = 'none';
        }

        function openLoanModal() {
            document.getElementById('loanModal').style.display = 'block';
        }

        function closeLoanModal() {
            document.getElementById('loanModal').style.display = 'none';
        }
    </script>

</body>

</html>