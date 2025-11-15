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
    password:
    <input type="password" name="password"> <br>
    Confirm Password:
    <input type="password" name="password"> <br>
    <input type="submit" name="submit" value="Register" >
  </form>
  <!-- End of Sign-Up Form-->
</body>
</html>

<?php 
  if($_SERVER["REQUEST_METHOD"] == "POST") { // Check if the method is post if yes
    // Variable for user
    $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
    // Variable for password
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);


    if(!$email){  // If email is missing
      echo "Email is Missing!";
    } else if (empty($password)) { // If password is empty
      echo "Password is Missing!";  
    } else { // Else hash the password for security
        $hash = password_hash($password, PASSWORD_DEFAULT); // hash
        $confirmation = $password;

        // SQL QUERY Insert data in the database
        $sql = "INSERT INTO users (email, password) 
        VALUES ('$email', '$hash')"; 

      try { // Try query
        if(!$confirmation){
          mysqli_query($conn, $sql);
          echo "You are now registered!";
        } else {
          echo "Password is not the same!";
        }
      } catch(mysqli_sql_exception) { // Catch Error
        echo "Email already signed up!  ";
      }
    }
  }
  mysqli_close($conn);
?>  
