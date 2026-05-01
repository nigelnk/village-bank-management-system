<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href = "../static/css/login.css" rel = "stylesheet">
</head>
<body>
    <div class="container">
        <div class="title">
        <h3>WELCOME TO MALAWILANO VILLAGE BANK </h3>
        </div>
        <div class="subtitle">
            <img src="c:\Users\PC\Downloads\Team spirit-pana (1).png">
        </div>
        <div class="form">
            <form method="post" action  = "log_in_page.php" >
                    <div class="username">
                    <input type="text"  id = "username" required placeholder="Email address/phone number">
                    </div>
                    <div class="pass">
                        <input type = "password" id ="password" required placeholder="password please">
                    </div>
                    <div class="login">
                        <input type = "submit" value="log in" id = "btn">
                    </div>
                    </form>
        
                    <div class="support">
                    <div class="forgotpas">
                        <button type="button" >forgot password?</button>
                    </div>
                    
                    <div class="create">
                        <button type="button" >create new account</button>
                    </div>
                    </div>
       </div>
    </div>
</body>
</html>
