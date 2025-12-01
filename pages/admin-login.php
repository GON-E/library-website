<?php 
  // 1. Start session FIRST
  session_start();
  include("../config/database.php");
  $error_message = "";

  if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = $_POST["password"];

    if(empty($admin) || empty($password)){
      $error_message = "All fields are required!"; 
    } else {
      
      $sql = "SELECT * FROM admins WHERE admin = ?";
      $stmt = mysqli_prepare($conn, $sql);

      if($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $admin);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) > 0 ) {
          $row = mysqli_fetch_assoc($result);
          $storedPassword = $row['password'];

          if(password_verify($password, $storedPassword)){
              
             session_regenerate_id(true);
             $_SESSION['is_admin_logged_in'] = true;
             $_SESSION['admin_name'] = $row['admin'];

             // THIS WILL NOW WORK!
             header("Location: ../pages/admin-dashboard.php");
             exit();

          } else {
            $error_message = "Incorrect Password";
          }
        } else {
           $error_message = "Account Not Found";
        }
        mysqli_stmt_close($stmt);
      }  
    } 
  }
  if(isset($conn)) { mysqli_close($conn); }
?>


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
  <link rel="icon" href="../images/lock.png" type="image/x-icon">
</head>
<body>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
    <?php if(!empty($error_message)): ?>
    <p style="color: red; text-align: center; background: #ffcccc; padding: 10px; border-radius: 5px;">
        <?php echo $error_message; ?>
    </p>
    <?php endif; ?>
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
          <span class="login-attempt attempt-count"><u>Login Attempts: 0</u></span>
        </section>
        <section>
          <input type="submit" name="login" value="Log In" class="login-button">  
        </section>  
      </section>
      <section class="form-footer">
        <span>Not an admin? <a href="../pages/public-homepage.php">Exit</a></span>
      </section>
    </section>
  </form>
  <script src="../script/admin-login.js"></script>
</body>
</html>