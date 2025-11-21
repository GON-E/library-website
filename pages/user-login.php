<?php 
  include("../config/database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Login</title>
  <link rel="stylesheet" href="../styles/user-login.css">
</head>
<body>
  <!-- Form -->
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>"method="post">
    Email:
    <input type="text" name="email" placeholder="email"> <br>
    Password:
    <input type="password" name="password" placeholder="password"> <br>
    <input type="submit" name="submit" value="submit">
  </form>
</body>
</html>

<?php 
  if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form Validation
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    
    // If email is missing 
    if(!$email) {
      echo "Email is missing";
    } else if(empty($password)) { // If Password is missing
      echo "Password is missing";
    } else { // If none is missing
      // sql query
      $sql = "SELECT * FROM users WHERE email = '$email'";
      try { // try to query
        $result = mysqli_query($conn, $sql);
        
      } catch (mysqli_sql_exception) { // catach fatal error
        echo "Error Occured!";
      }

      if(mysqli_num_rows($result) > 0){ // check if there is a result
        $storedPassword = null; // Accumulator variable
        $row = mysqli_fetch_assoc($result);  // fetch data from the db
        $storedPassword = $row['password']; // get the password stored in db
        
        if(password_verify($password, $storedPassword)) { // if the password and stored password is same
          // Session to store basic information
          $_SESSION['userId'] = $row['id'];
          $_SESSION['userName'] = $row['username'];

          header("location: "); // Redirect to a certain page

          exit();

        } else { // If not same
          echo "Incorrect Password!";
        }

      } else { // If there is no matching account
        echo "No Records of the Account!";
      }
    }
  
  }

?>
