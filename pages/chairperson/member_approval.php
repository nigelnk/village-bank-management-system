<?php
require_once '../../auth_check.php';
requireRole('Chairperson');
require_once '../../utils/config.php';

$conn = get_db();

// delete
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    $conn->query("DELETE FROM members WHERE user_id='$id'");
    $conn->query("DELETE FROM users WHERE id='$id'");

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// approve
if (isset($_POST['approve_id'])) {
    $id = $_POST['approve_id'];

    // change ember role
    $conn->query("
        UPDATE users 
        SET role_id = (SELECT role_id FROM roles WHERE role_name='member')
        WHERE id='$id'
    ");

    // update status
    $conn->query("
        UPDATE members 
        SET status='approved'
        WHERE user_id='$id'
    ");

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : "";

// only guest users
$where = "WHERE r.role_name = 'guest' AND m.user_id IS NOT NULL";

if (!empty($search)) {
    $where .= " AND (
        m.firstname LIKE '%$search%' 
        OR m.lastname LIKE '%$search%' 
        OR m.phone LIKE '%$search%' 
        OR m.location LIKE '%$search%'
    )";
}

// total count
$totalQuery = "
SELECT COUNT(*) as total 
FROM users u
JOIN roles r ON u.role_id = r.role_id
LEFT JOIN members m ON m.user_id = u.id
$where
";

$totalResult = $conn->query($totalQuery);
$totalRecords = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// main query
$query = "
SELECT u.id as user_id, m.*
FROM users u
JOIN roles r ON u.role_id = r.role_id
LEFT JOIN members m ON m.user_id = u.id
$where
LIMIT $limit OFFSET $offset
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Member Approval</title>

    <link rel="stylesheet" href="../../static/css/chairperson_layout.css">
    <link rel="stylesheet" href="../../static/css/chairperson_sidebar.css">
    <link rel="stylesheet" href="../../static/css/chairperson_topbar.css">
    <link rel="stylesheet" href="../../static/css/member.css">
</head>

<body>

    <?php include("../../includes/chairperson_sidebar.php"); ?>

    <?php
    $pageTitle = "Member Approval";
    include("../../includes/chairperson_topbar.php");
    ?>

    <div class="main">

        <!-- searchbar -->
        <div class="search-container">
            <form method="GET" class="search-form">
                <input
                    type="text"
                    name="search"
                    placeholder="Search pending members..."
                    value="<?= htmlspecialchars($search); ?>"
                    class="search-input">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <!-- table -->
        <div class="member-card">
            <table class="member-table">

                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>

                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['member_id'] ?></td>
                            <td><?= $row['firstname'] . " " . $row['lastname'] ?></td>
                            <td><?= $row['phone'] ?></td>
                            <td><?= $row['location'] ?></td>
                            <td><?= $row['status'] ?></td>

                            <td>
                                <button class="btn btn-edit" style="color: white;"
                                    onclick="openApproveModal('<?= $row['user_id'] ?>')">
                                    Approve
                                </button>

                                <button class="btn btn-delete"
                                    onclick="openDeleteModal('<?= $row['user_id'] ?>')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No pending members found</td>
                    </tr>
                <?php endif; ?>

            </table>

            <!-- paginationi -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="page-btn">← Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                        class="page-number <?= ($i == $page) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="page-btn">Next →</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- delete modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>⚠ Delete User</h3>
            <p>This action is permanent. Continue?</p>

            <form method="POST">
                <input type="hidden" name="delete_id" id="delete_id">

                <div class="modal-actions">
                    <button type="submit" class="btn-delete">Yes, Delete</button>
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- approve modal -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <h3>Approve Member</h3>
            <p>Approve this user and make them a full member?</p>

            <form method="POST">
                <input type="hidden" name="approve_id" id="approve_id">

                <div class="modal-actions">
                    <button type="submit" class="btn-edit">Yes, Approve</button>
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        function openDeleteModal(id) {
            document.getElementById('deleteModal').style.display = 'block';
            document.getElementById('delete_id').value = id;
        }

        function openApproveModal(id) {
            document.getElementById('approveModal').style.display = 'block';
            document.getElementById('approve_id').value = id;
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
            document.getElementById('approveModal').style.display = 'none';
        }
    </script>

</body>

</html>