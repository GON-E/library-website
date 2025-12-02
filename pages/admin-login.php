<?php

  // 1. Start session FIRST

  session_start();

  include("../config/database.php");

  $error_message = "";

    $max_attempts = 5;
  $lock_duration = 60; // 1 minute lock

  if (!isset($_SESSION['attempts'])) {
      $_SESSION['attempts'] = $max_attempts;
  }

  if (!isset($_SESSION['lock_time'])) {
      $_SESSION['lock_time'] = 0;
  }

  // check if locked
  if ($_SESSION['lock_time'] > time()) {
      $remaining = $_SESSION['lock_time'] - time();
      $error_message = "Too many attempts! Try again in $remaining seconds.";
  }//try

  if($_SERVER["REQUEST_METHOD"] == "POST"){

    if ($_SESSION['lock_time'] > time()) {
        $error_message = "You are locked. Try again in " . ($_SESSION['lock_time'] - time()) . " seconds.";
    } else {//try

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

             $_SESSION['attempts'] = $max_attempts;
$_SESSION['lock_time'] = 0;//try


             session_regenerate_id(true);

             $_SESSION['is_admin_logged_in'] = true;

             $_SESSION['admin_name'] = $row['admin'];



             // THIS WILL NOW WORK!

             header("Location: ../pages/admin-dashboard.php");

             exit();



          } else {

            $_SESSION['attempts']--;

    if ($_SESSION['attempts'] <= 0) {
        $_SESSION['lock_time'] = time() + $lock_duration;
        $error_message = "Too many attempts! Locked for 1 minute.";
    } else {
        $error_message = "Incorrect Password. Attempts left: " . $_SESSION['attempts'];
    }//try
  }
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

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

  <title>Admin Login</title>

  <link rel="stylesheet" href="../styles/ad-login.css">

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

        <img src="../images/bookTitle.png" alt="Logo" type="image" class="LOGO" width="100%" height="20%">

        <div class="login-text">

          <h3>Login</h3>

        </div>

      </section>

      <section class="sign-ups">

        <section>

          <input type="text" name="admin" placeholder="username"> <br>

        </section>

        <section class="password-section">

          <div class="password-wrapper">

          <input type="password" name="password" id="password" placeholder="password">

           <i id="show-password" class="fa-regular fa-eye"></i>

          <br>

    </div>

          <span class="login-attempt">
    <u>
        <?php
            if ($_SESSION['lock_time'] > time()) {
                echo "Locked for " . ($_SESSION['lock_time'] - time()) . "s";
            } else {
                echo "Attempts left: " . $_SESSION['attempts'];
            }
        ?>
    </u>
</span><!--try-->


   

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

 <script>

const passwordField = document.getElementById("password");

const showPassword = document.getElementById("show-password");



showPassword.addEventListener("click", () => {

    if (passwordField.type === "password") {

        passwordField.type = "text";

        showPassword.classList.remove("fa-eye");

        showPassword.classList.add("fa-eye-slash");

    } else {

        passwordField.type = "password";

        showPassword.classList.remove("fa-eye-slash");

        showPassword.classList.add("fa-eye");

    }

});//show password 
<?php if ($_SESSION['lock_time'] > time()): ?>
let remaining = <?php echo $_SESSION['lock_time'] - time(); ?>;
const attemptSpan = document.querySelector(".login-attempt u");

const timer = setInterval(() => {
    remaining--;
    attemptSpan.textContent = "Locked for " + remaining + "s";

    if (remaining <= 0) {
        clearInterval(timer);
         window.location.href = "admin-dashboard.php";
    }
}, 1000);
<?php endif; ?>
;



</script>

  <script src="../script/admin-login.js"></script>

</body>

</html>