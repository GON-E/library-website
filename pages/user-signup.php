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
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="signupForm">
    <div class="signup-container">
   <img src="../images/bookTitle.png" alt="Logo" type="image" class="LOGO" width="90%" height="10%">
    <p class="title"> SIGN-UP </p>
    <input type="email" placeholder="Email" name="email" required> 
    <input type="email" placeholder="Recovery Email" name="recoveryEmail" required>
    <input type="text" placeholder="username" name="username" required>
    <input type="password" placeholder="Password" name="password" required>
 
    

    <input type="password" placeholder="Confirm Password" name="confirmPassword" required>  
    <i class="fa-solid fa-eye" id= "toggleConfirm"></i>   
    <button class="btn-signup" name="register"> Sign-In </button>

    <br>
    <p class="btn-login"> Back to <a class="btn-login" href="../pages/user-login.php"> Log In</a> </p> 
   </div> 
   <div class="report-png"><a img src="report_btn.png" href="https://www.youtube.com/@awshumdude._" width="100%" height="100%"></a></div>
  </form>
  <script>
  (function(){
    const form = document.getElementById('signupForm');
    if(!form) return;
    form.addEventListener('submit', function(e){
      const email = (document.querySelector('input[name="email"]').value || '').trim();
      const recovery = (document.querySelector('input[name="recoveryEmail"]').value || '').trim();
      const password = document.querySelector('input[name="password"]').value || '';
      const confirm = document.querySelector('input[name="confirmPassword"]').value || '';

      if(email === recovery){
        alert('Recovery email must be different from your account email.');
        e.preventDefault();
        return;
      }
      if(password.length < 8){
        alert('Password must be at least 8 characters long.');
        e.preventDefault();
        return;
      }
      if(!/[^A-Za-z0-9]/.test(password)){
        alert('Password must include at least one special character.');
        e.preventDefault();
        return;
      }
      if(password !== confirm){
        alert('Passwords do not match.');
        e.preventDefault();
        return;
      }
    });
  })();
  </script>
</body>

</html>
