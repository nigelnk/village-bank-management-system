<?php

require_once '../../utils/config.php';
$conn = get_db();

    //fetching the data
    $query = "SELECT * FROM members";
    $result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Member Details</title>
    </head>
<body>
    <form action="" method="POST">
        <table border="1" align="center">
            <tr>
                <th>member_id</th>
                <th>firtsname</th>
                <th>lastname</th>
                <th>phone</th>
                <th>loaction</th>
                <th>next_of_kin</th>
                <th>gender</th>
                <th>status</th>
                <th>joined-date</th>
                <th>updated-date</th>
            </tr>

            <?php
            //looping through the database
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['member_id'] . "</td>";
                    echo "<td>" . $row['firstname'] . "</td>";
                    echo "<td>" . $row['lastname'] . "</td>";
                    echo "<td>" . $row['phone'] . "</td>";
                    echo "<td>" . $row['location'] . "</td>";
                    echo "<td>" . $row['next_of_kin'] . "</td>";
                    echo "<td>" . $row['gender'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "<td>" . $row['joined_date'] . "</td>";
                    echo "<td>" . $row['updated_at'] . "</td>";
                    echo "</tr>";
                }
            }
            else {
                echo "<tr><td> colspan='10'>No members found in database</td></tr>";
            }
            ?>
        </table>
        <br>

        <input type="Submit" value="Add member">
        &nbsp
        <input type="button" value="Remove member">
</body>
</html>