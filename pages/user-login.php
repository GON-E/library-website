<?php 
// Include the database connection and login logic file
include("../config/database.php");
include("../fetch/user-login-fetch.php");

// $error_message will be set by user-login-fetch.php if login fails
// Ensure that session_start() is called in user-login-fetch.php or database.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="../styles/user-log-in.css">
</head>
<body>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
        <section class="form-title">
            <h2>User Login</h2>
        </section>

        <?php if (!empty($error_message)): ?>
            <p style="color: red; text-align: center; margin-bottom: 15px; background: #ffebeb; padding: 10px; border-radius: 5px;">
                <?php echo $error_message; ?>
            </p>
        <?php endif; ?>

        <section>
            <input type="text" name="email" placeholder="email" required> 
        </section>
        <section>
            <input type="password" name="password" placeholder="password" required> <br>
        </section>
        <section>
            <input type="submit" name="submit" value="Log In" class="submit-btn">
        </section>
        <section class="links">
            <h5><a href="../pages/reset-password.php">forgot password</a></h5>
            
            <h5>Don't have an account?<a href="../pages/user-signup.php">Sign Up</a></h5>
        </section>
    </form>

</body>
</html>