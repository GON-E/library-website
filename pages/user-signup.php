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
    <link rel="stylesheet" href="../styles/user-signup.css">
</head>
<body>
   <div class="signup-container">
   <img src="../images/bookTitle.png" alt="Logo" type="image" class="LOGO" width="90%" height="10%">
    <p class="title"> SIGN-UP </p>
    <input type="email" placeholder="Email" name="email"> 
    <input type="text" placeholder="Recovery Information" name="recovery">
    <input type="password" placeholder="Password">  <i class="fa-solid fa-eye"></i>
    <i class="fa-solid fa-eye" id="togglePassword"></i>
    <input type="password" placeholder="Confirm Password">  
    <i class="fa-solid fa-eye" id= "toggleConfirm"><i>   
    <button class="btn-signup"> Sign-In </button>
    
    <br>
    
    <p class="btn-login"> Back to <a class="btn-login" href="#"> Log In</a> </p> 
   </div> 
   <div class="report-png"><a img src="report_btn.png" href="https://www.youtube.com/@awshumdude._" width="100%" height="100%"></a></div>
  
</body>

<footer>Copyright © 2025 Lé Bros: Library This system is for education puposes only. </footer>
</html>
