<?php 
  include("../config/database.php");
  include("../fetch/user-signup-fetch.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../styles/user-sign-up.css">
        <link rel="icon" href="../images/icons/bookIcon.png" type="image/png">  

</head>
<body>
  <form <?php echo htmlspecialchars($_SERVER["PHP_SELF"])?> method="post">
    <div class="signup-container">
   <img src="../images/bookTitle.png" alt="Logo" type="image" class="LOGO" width="90%" height="10%">
    <p class="title"> SIGN-UP </p>
    <input type="email" placeholder="Email" name="email" required> 
    <input type="text" placeholder="Recovery Email" name="recoveryEmail" required>
    <input type="text" placeholder="username" name="username" required>
    <input type="password" placeholder="Password" name="password" required>
 
    

    <input type="password" placeholder="Confirm Password" name="confirmPassword">  
    <i class="fa-solid fa-eye" id= "toggleConfirm"></i>   
    <button class="btn-signup" name="register"> Sign-In </button>

    <br>
    <p class="btn-login"> Back to <a class="btn-login" href="../pages/user-login.php"> Log In</a> </p> 
   </div> 
   <div class="report-png"><a img src="report_btn.png" href="https://www.youtube.com/@awshumdude._" width="100%" height="100%"></a></div>
  </form>
</body>

</html>
