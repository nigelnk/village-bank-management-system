<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sign Up</title>
<link href="../../static/css/addmember.css" rel="stylesheet">
</head>

<body>
    
    <h2>Join Our Group</h2>

    <div class="container">
        <div class="sidebar">
            <h2>Village Bank</h2>
            <ul>
            <li> <a href="#">Dashboard</a></li>
            <li> <a href="#">Dashboard</a></li>
            <li> <a href="#">Dashboard</a></li>
            <li> <a href="#">Dashboard</a></li>
            <li> <a href="#">Dashboard</a></li>
            <li> <a href="#">Dashboard</a></li>
            <li> <a href="#">Dashboard</a></li>
                
            </ul>
             </div>
             
<form method="POST" action="">
   
    <div class="main-content">

         <!--Personal Details-->
    <h4>Personal Details</h4>
    <div class="form-row">
<div class="form-group">
    <label for="fname">First Name:</label>
    <input type="text" name="fname" required>
    </div>
    <div class="form-group">
    <label for="lname">Last Name:</label>
    <input type="text" name="lname" required>
    </div>
    <div class="form-group">
        <div class="gender-group">
    <label for="gender">Gender:</label> 
    <label for="female">Female</label>
    <input type="radio" name="gender">
    <label for="male">Male</label>
    <input type="radio" name="gender">
</div>
    </div>
    <div class="form-group">
    <label for="id">National ID Number:</label>
    <input type="text" name="id">
</div>
</div>

    <!--Contact Information-->
    <h4>Contact Details</h4>
    <div class="form-row">
    <label for="phonenumber">Phone Number:</label>
    <input type="text" name="phonenumber">
    <label for="address">Physical Address:</label>
    <textarea  name="address" id="address"></textarea>
</div>
    <!--Next of Kin-->
    <h4>Emergency Contact</h4>
    <div class="form-row">
    <label for="fullname">Full Name:</label>
    <input type="text" name="fullname">
    <label for="relationship">Relationship to member:</label>
    <select name="relationship" id="relationship">
        <option value="sibling">Sibling</option>
        <option value="parent">Parent</option>
        <option value="spouse">Spouse</option>
        <option value="other">Other</option>
    </select>
    <label for="pnumber">Phone Number:</label>
    <input type="text" name="pnumber">
</div>

<div class="buttons">
<button type="submit" class="save" name="save">Save Member</button>
<button type="reset" class="reset" name="reset">Reset</button>
</div> </div> </div>
</form>
</body>

</html>