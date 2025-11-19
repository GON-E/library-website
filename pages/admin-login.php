<?php 
  include("../config/database.php");
?>

<!--HTML STRUCTURE-->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link rel="stylesheet" href="admin-login.css">
</head>
<body>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
    Admin: 
    <input type="text" name="admin"> <br>
    Password:
    <input type="password" name="password"> <br>
    <input type="submit" name="login" value="login">    
  </form>
</body>
</html>
<!-- END OF HTML STRUCTURE -->

<?php 
  if($_SERVER["REQUEST_METHOD"] == "POST"){
    $admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

    if(empty($admin) || empty($password)){
      echo "All field are required!";
    } else {
      $sql = "SELECT * FROM admins WHERE admin = '$admin'";

      try {
        $result = mysqli_query($conn, $sql);
      } catch(mysqli_sql_exception){
        echo "An Error Occured!";
      }
      
      if(mysqli_num_rows($result) > 0){
        echo "Admin Found";
        $storedPassword = null;
        $row = mysqli_fetch_assoc($result);

        $storedPassword = $row['password'];
        
        if(password_verify($password, $storedPassword)){
          $_SESSION['admin'] = $row['admin'];

          header("location: ");

          exit();
        } 
      }else {
        echo "No Account Found!";
      }
    }
  }
  mysqli_close($conn);
?>