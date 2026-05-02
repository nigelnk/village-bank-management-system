<!DOCTYPE html>
<html lang="en">

<head>
    <title>Village Bank Login</title>

    <link rel="stylesheet" href="../static/css/signin.css">

</head>

<body>

    <div class="container">

        <div class="title">
            <h3>WELCOME TO MALAWILANO VILLAGE BANK</h3>
        </div>

        <div class="subtitle">
            <img src="../../static/photos/logo.png" alt="Village Bank Logo">
        </div>

        <div class="form">

            <form method="post" action="log_in_page.php">

                <div class="username">
                    <input type="text" name="username" required placeholder="username">
                </div>

                <div class="pass">
                    <input type="password" name="password" required placeholder="Enter password">
                </div>

                <div class="login">
                    <input type="submit" value="Log In">
                </div>

            </form>

            <div class="support">

                <div class="forgotpas">
                    <button type="button">Forgot Password?</button>
                </div>

                <div class="create">
                    <button type="button">Create New Account</button>
                </div>

            </div>

        </div>

    </div>

</body>

</html>
