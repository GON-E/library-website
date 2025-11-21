<?php 
  include("../config/database.php");

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

          header("location: ../actions/add-book.php");

          exit();
        } 
      }else {
        echo "No Account Found!";
      }
    }
  }
  mysqli_close($conn);
?>

<!--HTML STRUCTURE-->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Alice&family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&family=Pacifico&family=Roboto:ital,wght@0,100..900;1,100..900&family=SUSE+Mono:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
  <title>Admin Login</title>
  <link rel="stylesheet" href="../styles/admin-login.css">
</head>
<body>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
  <section class="form-container">
    <section class="form-title">
      <h2 class="Library-name">Le Bros: Library</h2>
      <div class="login-text">
        <h3>Login</h3>
      </div>
    </section>
    <section class="sign-ups">
      <section>
        <input type="text" name="admin" placeholder="username"> <br>
      </section>
      <section class="password-section">
        <input type="password" name="password" placeholder="password"> <br>
        <span class="login-attempt"><u>Login Attempts: 0
        </u></span>
      </section>
      <section>
        <input type="submit" name="login" value="Log In" class="login-button">  
      </section>  
    </section>
    <section class="form-footer">
      <span>Not an admin?
        <a>Back to login</a>
      </span>
    </section>
  </section>
  </form>
</body>
</html>
<!-- END OF HTML STRUCTURE -->