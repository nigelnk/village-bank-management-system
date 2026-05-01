<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../static/css/signup.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
</head>
<body>
<div class="container">
    <div class="head">
        <h2>Creating Account</h2>
    </div>
    <div class="form">
        <form action="" method="post">
    <div class="firstname">
<label for="fname" >First name :</label>
<input type="text" id="fname" required>
    </div>
    <div class="lastname">
        <label for="lname">Last name :</label>
        <input type="text" id = "lname" required>
    </div>
    <div class="gender">
        <label for="gender" >Gender :</label>         <input type="radio" name="gender" id="M">
        <label for="M" >Male</label> 
        <input type="radio" name="gender" id="F">
        <label for="F">Female </label>
    </div>
    <div class="phone">
        <label for="phone">Phone number :</label>
        <input type="tel" id = "phoneNO" pattern="[0-9]{10}" maxlength="10" placeholder="0998004466" required>
    </div>
    <div class="location">
        <label for="address">Home Address :</label>
        <input type="text"  id = "home" placeholder="e.g village" >
    </div>
    <div class="next-kin">
        <div class="name">  <label for="kin" >Next_of_kin :</label>
        <input type="text" id ="next-kin" >
        </div>
        <!-- <div class="phone">
            <input type="number" placeholder="+265998004466">
        </div> -->
    </div>
    <div class="status">
        <label for="status" >Status :</label>
        <input type="text" id = "status">
    </div>
    <div class="Joined_date">
        <label for="Date">Date of Join :</label>
        <input type="date" id = "date_of_join" >
    </div>
    <!-- <div class="updated_at">
        <label for="updated">Date of update :</label>
        <input type="date">
    </div> -->
    <div class="submit_cancel">
        <div class="submit">
            <input type="submit" value="submit">
        </div>
        <div class="cancel">
            <input type="reset" value="Cancel">
        </div>
    </div>

        </form>
    </div>
</div>


   
</body>
</html>
