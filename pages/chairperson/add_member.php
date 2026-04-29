<!DOCTYPE html>
<html>
<head>
    <title>Add Member</title>
<style>
    body{
    font-family:'Times New Roman', Times, serif;
    background-color: #f4f6f8;
   }
   .container{
    max-width:900px;
    margin:auto;
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
   }
   h2{
      text-align: center;
      font-size:18pt;
    
   }

   h4{
    color:#2c3e50;
    font-size: 15pt;
   }
  .form-row{
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap:15px;

  }
  .form-group{
    display:flex;
    flex-direction: column;
  }
  .form-group label{
    margin-bottom: 5px;

  }

  .form-group input, .form-group select{
    padding: 10px;
    border: 1px solid #ccc;
    border-radius:2px;
  }
  .full{
    grid-column:span 2;
  }
  .gender-group{
    display:flex;
    gap: 10px;
    align-items: center;
  }
  .gender-group input[type=radio]{
accent-color: rgb(4,99,37);
  }
  
  .buttons{
    display: flex;
    gap:10px;
    margin-top:20px;
  }
    button{
        padding:10px;
        width:32%;
        border:none;
        border-radius: 5px;
        font-size:11pt;
        cursor: pointer;
        flex:1;
    }
   .save{
    background-color: rgb(4, 99, 37);
    color:antiquewhite;
   }
    
   .reset{
    background-color: rgb(41, 41, 198);
    color:antiquewhite;
   }
   input, select, textarea{
    width:95%;
    padding:10px;
    border-radius: 1px solid #ccc;
    font-size:14px;
   }

</style>
</head>
<body>
    
    <h2>Add New Member</h2>
<form method="POST">
    <!--Personal Details-->
    <div class="container">
    <h4>Personal Details</h4>
    <div class="form-row">
        <div class="form-group">
    <label for="photo">Profile Photo:</label>
    <input type="file" name="photo">
</div>
<div class="form-group">
    <label for="fname">First Name:</label>
    <input type="text" required>
    </div>
    <div class="form-group">
    <label for="lname" required>Last Name:</label>
    <input type="text">
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
    <label for="dob">Date of Birth:</label>
    <input type="date" min="25/01/08">
    </div>
    <div class="form-group">
    <label for="id">National ID Number:</label>
    <input type="text">
</div>
</div>

    <!--Contact Information-->
    <h4>Contact Details</h4>
    <div class="form-row">
    <label for="phonenumber">Phone Number:</label>
    <input type="text">
    <label for="othernumber">Alternative Phone Number:</label>
    <input type="text">
    <label for="email">Email:</label>
    <input type="email">
    <label for="address">Physical Address:</label>
    <textarea  name="address" id="address"></textarea>
</div>
    <!--Next of Kin-->
    <h4>Emergency Contact</h4>
    <div class="form-row">
    <label for="fullname">Full Name:</label>
    <input type="text">
    <label for="relationship">Relationship to member:</label>
    <select name="relationship" id="relationship">
        <option value="sibling">Sibling</option>
        <option value="parent">Parent</option>
        <option value="spouse">Spouse</option>
        <option value="other">Other</option>
    </select>
    <label for="pnumber">Phone Number:</label>
    <input type="text">
</div>
    <!--Financial Details-->
    <h4>Financial Details</h4>
    <div class="form-row">
    <label for="occupation">Occupation:</label>
    <input type="text">
    <label for="income">Estimated Monthly Income:</label>
    <input type="number" min="100000">
    <label for="accountnumber">Account Number:</label>
    <input type="text"> <br> <br> <br>
</div>

<div class="buttons">
<button type="submit" class="save">Save Member</button>
<button type="reset" class="reset">Reset</button>
</div> </div>
</form>
<?php

?>
</body>
</html>