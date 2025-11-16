<?php 
  include("../config/database.php");
?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SIGN UP </title>
</head>
<body>
  <!-- Sign-Up Form -->
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?> " method="post">
    email:
    <input type="email" name="email"> <br>
    username:
    <input type="text" name="username"> <br>
    password:
    <input type="password" name="password"> <br>
    Confirm Password:
    <input type="password" name="confirmPassword"> <br>
    <input type="submit" name="submit" value="Register" >
  </form>
  <!-- End of Sign-Up Form-->
</body>
</html>

<?php 
  if($_SERVER["REQUEST_METHOD"] == "POST") { // Check if the method is post if yes
    // Variable for user
    $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
    // Variable for username
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
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

        // SQL QUERY Insert data in the database
        $sql = "INSERT INTO users (email,username,password) 
        VALUES ('$email','$username','$hash')"; 

      try { // Try query
        mysqli_query($conn, $sql);
        echo "You are now Registered!";   
      }catch(mysqli_sql_exception $err) { // Catch Error
        if($err -> getCode() == 1062){
          echo "Email already signed up!";
        } else {
          echo "An Error Occured, Please Try Again!". $err -> getMessage();
        }
      }
    }
  }
}
  mysqli_close($conn);
?>  
