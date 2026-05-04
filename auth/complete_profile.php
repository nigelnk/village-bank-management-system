<?php

//ensure the user is logged in and has guest permissions
require_once '../auth_check.php';
requireRole('Guest');

//database connection setup
require_once '../utils/config.php'; 
$conn = get_db();

if (!isset($_GET['user_id']) && !isset($_POST['user_id'])) {
    die("Invalid access.");
}

$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : $_GET['user_id'];

//checks if the form was submitted
if (isset($_POST['save'])) {

    //capture data from form inputs
    $firstname = $_POST['fname'];
    $lastname = $_POST['lname'];
    $gender = $_POST['gender'];
    $phonenumber = $_POST['phonenumber'];
    $location = $_POST['address'];
    $nextofkin_name = $_POST['fullname'];
    $relationship = $_POST['relationship'];
    $nextofkinnumber = $_POST['pnumber'];

    // prevent duplicate profile
    $check = $conn->query("SELECT * FROM members WHERE user_id = '$user_id'");
    if ($check->num_rows > 0) {
        die("Profile already completed.");
    }

    $sql = "INSERT INTO members 
    (user_id, firstname, lastname, phone, location, next_of_kin_name, next_of_kin_phone, relationship, gender, status, joined_date)
    VALUES 
    ('$user_id','$firstname','$lastname','$phonenumber','$location','$nextofkin_name','$nextofkinnumber','$relationship','$gender','pending',NOW())";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>
            alert('Profile completed! Waiting for approval');
            window.location.href = '../auth/waiting_approval.php';
        </script>";
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Profile</title>
    <link href="../static/css/complete_profile.css" rel="stylesheet">
</head>
<body>

    <header class="top-bar">
        <div class="logo">
            <img src="../static/photos/logo.jpeg" alt="Logo">
        </div>
        <div class="logout-container">
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <main class="main-content">
        <h2>Complete Your Profile</h2>
        <div class="container">
            <form method="POST" action="">

                <!-- p. Details -->
                <section class="form-section">
                    <h4>Personal Details</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fname">First Name</label>
                            <input type="text" name="fname" required>
                        </div>
                        <div class="form-group">
                            <label for="lname">Last Name</label>
                            <input type="text" name="lname" required>
                        </div>
                        <div class="form-group gender-group">
                            <label>Gender</label>
                            <label><input type="radio" name="gender" value="Female" required> Female</label>
                            <label><input type="radio" name="gender" value="Male" required> Male</label>
                        </div>
                        <div class="form-group">
                            <label for="id">National ID Number</label>
                            <input type="text" name="id" required>
                        </div>
                    </div>
                </section>

                <!-- Contact Details -->
                <section class="form-section">
                    <h4>Contact Details</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phonenumber">Phone Number</label>
                            <input type="text" name="phonenumber" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Physical Address</label>
                            <textarea name="address" id="address" required></textarea>
                        </div>
                    </div>
                </section>

                <!-- Emergency contacts -->
                <section class="form-section">
                    <h4>Emergency Contact</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fullname">Full Name</label>
                            <input type="text" name="fullname" required>
                        </div>
                        <div class="form-group">
                            <label for="relationship">Relationship</label>
                            <select name="relationship" id="relationship" required>
                                <option value="sibling">Sibling</option>
                                <option value="parent">Parent</option>
                                <option value="spouse">Spouse</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pnumber">Phone Number</label>
                            <input type="text" name="pnumber" required>
                        </div>
                    </div>
                </section>

                <div class="buttons">
                    <button type="submit" class="save" name="save">Save Profile</button>
                    <button type="reset" class="reset" name="reset">Reset</button>
                </div>

            </form>
        </div>
    </main>

</body>
</html>


