<?php

require_once '../../utils/config.php';

$conn = get_db();
$result = $conn->query("SELECT * FROM reports ORDER BY date_generated DESC");
?>

<h2>Reports</h2>

<?php while($row = $result->fetch_assoc()): ?>
    <div>
        <strong><?php echo $row['title']; ?></strong>
        <br>
        <a href="../<?php echo $row['file_path']; ?>" target="_blank">
            View PDF
        </a>
    </div>
<?php endwhile; ?>