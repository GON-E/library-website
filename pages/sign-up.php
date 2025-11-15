<?php 
  include("../config/database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <!-- Sign-Up Form -->
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?> " method="post">
    email:
    <input type="text" name="email"> <br>
    password:
    <input type="password" name="password"> <br>
    <input type="submit" name="submit" value="Register" >
  </form>
  <!-- End of Sign-Up Form-->
</body>
</html>

<?php 
  if($_SERVER["REQUEST_METHOD"] == "POST") { // Check if the method is post if yes
    // Variable for user
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_SPECIAL_CHARS);
    // Variable for password
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    $pattern = '/^[a-zA-Z]*$/'; // Pattern for username

    
    if(preg_match($pattern, $user)){ // Check if the email only contains letters 
      echo "email is Valid <br>"; // If Correct
    } else { // Else 
      echo "email is Invalid! Please enter only characters (no numbers). <br>";
    }

    if(empty($user)){  // If admin is empty
      echo "email is Missing!";
    } else if (empty($password)) { // If password is empty
      echo "Password is Missing!";  
    } else { // Else hash the password for security
        $hash = password_hash($password, PASSWORD_DEFAULT); // hash

        // SQL QUERY Insert data in the database
        $sql = "INSERT INTO admins (admin, password) 
        VALUES ('$admin', '$hash')"; 

      try { // Try query  
        mysqli_query($conn, $sql);
        echo "You are now registered!";
      } catch(mysqli_sql_exception) { // Catch Error
        echo "email is already taken!";
      }
    }
  }
  mysqli_close($conn);
?>  
