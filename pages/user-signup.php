<?php 
  include("../config/database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="user-signup.css">
</head>
<body>
   <div class="signup-container">
    <img src="LOGO.png" alt="Logo" width="100%">
    <p class="title"> SIGN-UP </p>
    <input type="email" placeholder="Email"> 
    <input type="text" placeholder="Recovery Information">
    <input type="password" placeholder="Password">  <i class="fa-solid fa-eye"></i>
    <input type="password" placeholder="Confirm Password">     
    <button class="btn-signup"> Sign-In </button>
    
    <br>
    
    <p class="btn-login"> Back to <a class="btn-login" href="#"> Log In</a> </p> 
   </div> 
   <div class="report-png"><a img src="report_btn.png" href="https://www.youtube.com/@awshumdude._" width="100%" height="100%"></a></div>
  
</body>

<footer>Copyright © 2025 Lé Bros: Library <br>This system is for education puposes only. </footer>
</html>

<?php 
  if($_SERVER["REQUEST_METHOD"] == "POST") { // Check if the method is post if yes
    // Variable for user
    $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
    // Variable for username
    $username = filter_input(INPUT_POST, "username", filter: FILTER_SANITIZE_SPECIAL_CHARS);
    // Variable for password
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    $confirmPassword = filter_input(INPUT_POST, "confirmPassword", FILTER_SANITIZE_SPECIAL_CHARS);

    if(!$email){  // If email is missing
      echo "Email is Missing!";
    } else if (empty($password)) { // If password is empty
      echo "Password is Missing!";  
    } else if(empty($username)) {
      echo "Username is Missing!";
    } else { // Else hash the password for security

      if($password != $confirmPassword){
        echo "Password do not match!";
      } else {
        $hash = password_hash(password: $password, algo: PASSWORD_DEFAULT); // hash

        // SQL QUERY Prepared Statement (Avoid Sql-injection)
        $sql = "INSERT INTO users (email,username,password)
        VALUES (?,?,?)";

        // Preparing to send the SQL without the real data
        $stmt = $conn -> prepare($sql);
        
        // If preparation failed
        if (!$stmt){
          die('Preparation Failed: ' .$conn -> error);
        }

        // Attach the real PHP variable s stands for string
        $stmt -> bind_param('sss',$email,$username, $hash);

      try { // Try query
        // Send the query
        $stmt -> execute();
        header('Location: ');
      }catch(mysqli_sql_exception $err) { // Catch Error
        if($err -> getCode() == 1062){
          echo "Email already signed up!";
        } else {
          echo "An Error Occured, Please Try Again!";
        }
      }
    }
  }
}
  mysqli_close($conn);
?>  
