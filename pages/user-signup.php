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
