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
    Email:
    <input type="text" name="email" placeholder="email"> <br>
    Password:
    <input type="password" name="password" placeholder="password"> <br>
    <input type="submit" name="submit" value="submit">
  </form>
</body>
</html>

