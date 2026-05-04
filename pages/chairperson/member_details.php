<?php
require_once '../../auth_check.php';
requireRole('Chairperson');
require_once '../../utils/config.php';

$conn = get_db();

// Handle Edit Form Submission
if (isset($_POST['edit_id'])) {
    $edit_id = (int)$_POST['edit_id'];
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $location = $conn->real_escape_string($_POST['location']);
    // $status = $conn->real_escape_string($_POST['status']);
    $status = "";
    $role_id = !empty($_POST['role_id']) ? (int)$_POST['role_id'] : null;


    if ($role_id == 4) {
        $status = "pending";
    }

    else {
        $status = "approved";
    }

    // Update members table
    $conn->query("
        UPDATE members 
        SET firstname='$firstname', lastname='$lastname', phone='$phone', location='$location', status='$status', updated_at=NOW()
        WHERE member_id=$edit_id
    ");

    // Update role in users table
    if ($role_id !== null) {
        $conn->query("
            UPDATE users u
            JOIN members m ON m.user_id = u.id
            SET u.role_id=$role_id
            WHERE m.member_id=$edit_id
        ");
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Delete Form Submission
if (isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    $conn->query("DELETE FROM members WHERE member_id=$delete_id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Search setup
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : "";

$where = "WHERE r.role_name != 'Guest'";


if (!empty($search)) {
    $where .= " AND (firstname LIKE '%$search%' OR lastname LIKE '%$search%' OR phone LIKE '%$search%' OR location LIKE '%$search%')";
}

// query to get members excluding Guest roles
$query = "
SELECT m.*, u.role_id, r.role_name 
FROM members m
LEFT JOIN users u ON m.user_id = u.id
LEFT JOIN roles r ON u.role_id = r.role_id
$where
LIMIT $limit OFFSET $offset
";
$result = $conn->query($query);

// get roles for modal dropdown
$rolesResult = $conn->query("SELECT * FROM roles");
$roles = [];
while ($role = $rolesResult->fetch_assoc()) {
    $roles[] = $role;
}
// Get total records for pagination, excluding Guests
$totalQuery = "
    SELECT COUNT(*) as total 
    FROM members m
    LEFT JOIN users u ON m.user_id = u.id
    LEFT JOIN roles r ON u.role_id = r.role_id
    WHERE r.role_name != 'Guest'
";

// If there’s a search term, add it
if (!empty($search)) {
    $totalQuery .= " AND (firstname LIKE '%$search%' OR lastname LIKE '%$search%' OR phone LIKE '%$search%' OR location LIKE '%$search%')";
}

$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

$result = $conn->query($query);

// get roles for modal dropdown
$rolesResult = $conn->query("SELECT * FROM roles");
$roles = [];
while ($role = $rolesResult->fetch_assoc()) {
    $roles[] = $role;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Member Details</title>
    <link rel="stylesheet" href="../../static/css/chairperson_layout.css">
    <link rel="stylesheet" href="../../static/css/chairperson_sidebar.css">
    <link rel="stylesheet" href="../../static/css/chairperson_topbar.css">
    <link rel="stylesheet" href="../../static/css/member.css">
</head>

<body>

    <?php include("../../includes/chairperson_sidebar.php"); ?>
    <?php
    $pageTitle = "View Member Dashboard";
    include("../../includes/chairperson_topbar.php");
    ?>

    <div class="main">
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search members..." value="<?= htmlspecialchars($search) ?>" class="search-input">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <div class="member-card">
            <table class="member-table">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Role</th>
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
                            <td><?= htmlspecialchars($row['role_name'] ?? 'No Role') ?></td>
                            <td>
                                <button class="btn btn-edit"
                                    onclick="openEditModal(
                                    '<?= $row['member_id'] ?>',
                                    '<?= $row['firstname'] ?>',
                                    '<?= $row['lastname'] ?>',
                                    '<?= $row['phone'] ?>',
                                    '<?= $row['location'] ?>',
                                    '<?= $row['status'] ?>',
                                    '<?= $row['role_id'] ?>'
                                )">
                                    Edit
                                </button>
                                <button class="btn btn-delete" onclick="openDeleteModal('<?= $row['member_id'] ?>')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No members found</td>
                    </tr>
                <?php endif; ?>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="page-btn">← Prev</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="page-number <?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
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
            <h3>⚠ Delete Member</h3>
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

    <!-- edit modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Member</h3>
            <form method="POST">
                <input type="hidden" name="edit_id" id="edit_id">
                <input type="text" name="firstname" id="firstname" placeholder="First Name"><br><br>
                <input type="text" name="lastname" id="lastname" placeholder="Last Name"><br><br>
                <input type="text" name="phone" id="phone" placeholder="Phone"><br><br>
                <input type="text" name="location" id="location" placeholder="Location"><br><br>
                <select name="role_id" id="role_id">
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['role_id'] ?>"><?= $role['role_name'] ?></option>
                    <?php endforeach; ?>
                </select><br><br>
                <div class="modal-actions">
                    <button type="submit" class="btn-edit">Save</button>
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

        function openEditModal(id, fname, lname, phone, location, status, role_id) {
            document.getElementById('editModal').style.display = 'block';
            document.getElementById('edit_id').value = id;
            document.getElementById('firstname').value = fname;
            document.getElementById('lastname').value = lname;
            document.getElementById('phone').value = phone;
            document.getElementById('location').value = location;
            document.getElementById('status').value = status;
            document.getElementById('role_id').value = role_id;
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
            document.getElementById('editModal').style.display = 'none';
        }
    </script>

</body>

</html>