<?php
require_once '../../utils/config.php';
$conn = get_db();
// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

// Search setup
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : "";

// query with search
$where = "";
if (!empty($search)) {
    $where = "WHERE firstname LIKE '%$search%' 
              OR lastname LIKE '%$search%' 
              OR phone LIKE '%$search%' 
              OR location LIKE '%$search%'";
}

// Get total records (for pagination)
$totalQuery = "SELECT COUNT(*) as total FROM members $where";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];

$totalPages = ceil($totalRecords / $limit);

// Main query with LIMIT
$query = "SELECT * FROM members $where LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
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
                <input
                    type="text"
                    name="search"
                    placeholder="Search members..."
                    value="<?php echo htmlspecialchars($search); ?>"
                    class="search-input">
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
                    <th>Actions</th>
                </tr>

                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?= $row['member_id'] ?></td>
                    <td><?= $row['firstname'] . " " . $row['lastname'] ?></td>
                    <td><?= $row['phone'] ?></td>
                    <td><?= $row['location'] ?></td>
                    <td><?= $row['status'] ?></td>

                    <td>
                        <button class="btn btn-edit"
                            onclick="openEditModal(
                            '<?= $row['member_id'] ?>',
                            '<?= $row['firstname'] ?>',
                            '<?= $row['lastname'] ?>',
                            '<?= $row['phone'] ?>',
                            '<?= $row['location'] ?>',
                            '<?= $row['status'] ?>'
                        )">
                            Edit
                        </button>

                        <button class="btn btn-delete"
                            onclick="openDeleteModal('<?= $row['member_id'] ?>')">
                            Delete
                        </button>
                    </td>
                </tr>

                <?php
                    }
                } else {
                    echo "<tr><td colspan='6'>No members found</td></tr>";
                }
                ?>

            </table>
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

    <!-- DELETE MODAL -->
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

    <!-- EDIT MODAL -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Member</h3>

            <form method="POST">
                <input type="hidden" name="edit_id" id="edit_id">

                <input type="text" name="firstname" id="firstname" placeholder="First Name"><br><br>
                <input type="text" name="lastname" id="lastname" placeholder="Last Name"><br><br>
                <input type="text" name="phone" id="phone" placeholder="Phone"><br><br>
                <input type="text" name="location" id="location" placeholder="Location"><br><br>
                <input type="text" name="status" id="status" placeholder="Status"><br><br>

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

        function openEditModal(id, fname, lname, phone, location, status) {
            document.getElementById('editModal').style.display = 'block';

            document.getElementById('edit_id').value = id;
            document.getElementById('firstname').value = fname;
            document.getElementById('lastname').value = lname;
            document.getElementById('phone').value = phone;
            document.getElementById('location').value = location;
            document.getElementById('status').value = status;
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
            document.getElementById('editModal').style.display = 'none';
        }
    </script>

</body>

</html>