<?php 
  include("../config/database.php");
  include("../fetch/user-login-fetch.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Login</title>
  <link rel="stylesheet" href="../styles/user-login.css">
</head>
<body>

  <!-- Form -->
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>"method="post">
    <section class="form-title">
      <h2>User Login</h2>
    </section>
    <section>
    <input type="text" name="email" placeholder="email"> 
    </section>
    <section>
    
    <input type="password" name="password" placeholder="password"> <br>
    </section>
    <section>
    <input type="submit" name="submit" value="submit" class="submit-btn">
    </section>
    <section class="links">
      <h6><a href="../pages/reset-password.php">forgot password</a></h6>
    </section>
  </form>

</body>
</html>

