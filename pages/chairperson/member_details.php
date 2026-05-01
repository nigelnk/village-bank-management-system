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
        <link rel="stylesheet" href="../../static/css/member.css">
        <title>Member Details</title>
    </head>
    <body>
        <h2 align="center"><u>Member Details</u></h2>
         
        <form action="" method="POST">
        <!--used atable for better structure-->
            <table class="member-table" border="1" cellspacing="0">
                <tr>
                    <td>
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
                                <li><a href="#">View Reports</a></li>
                                <li><a href="#">Profile</a></li>
                            </ul>
                                <a href="" id="add-member-btn">+ Add Member</a>
                                <a href="" id="remove-member-btn"> Remove member</a>
                        </nav>  
                    </td>
                </tr>
                
                <tr>
                    <th>member_id</th>
                    <th>firstname</th>
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
        </form>
    </body>
</html>