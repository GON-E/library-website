  <?php
  // fetch/user-signup-fetch.php - Handles user registration with password hashing
  
  // Load database connection
  include_once("../config/database.php");

  // Check if form was submitted using POST method
  if($_SERVER["REQUEST_METHOD"] == "POST") { 
    // Get email from form and validate it as a real email
    $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
    // Get username from form and sanitize special characters
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
    // Get recovery email from form (used for password recovery)
    $recovery_em = filter_input(INPUT_POST, "recoveryEmail");
    // Get password from form (use null coalescing to avoid undefined index)
    $password = $_POST["password"] ?? "";
    // Get password confirmation from form
    $confirmPassword = $_POST["confirmPassword"] ?? "";

    // Check if email or recovery email is missing
    if(!$email || !$recovery_em) {
      echo "Email or Recovery Email is Missing!";
    } else if (empty($password)) { 
      // Check if password is empty
      echo "Password is Missing!";  
    } else if(empty($username)) {
      // Check if username is empty
      echo "Username is Missing!";
    } else { 
      // All fields are provided, now validate password match

      // Check if password and confirm password match
      if($password != $confirmPassword){
        echo "Passwords do not match!";
      } else {
        // Passwords match! Hash the password using bcrypt for security
        $hash = password_hash(password: $password, algo: PASSWORD_DEFAULT);

        // SQL query to insert new user with prepared statement (prevents SQL injection)
        $sql = "INSERT INTO users (email,recovery_em,username,password)
        VALUES (?,?,?,?)";

        // Prepare the SQL statement without the real values yet
        $stmt = $conn -> prepare($sql);
        
        // Check if SQL preparation failed
        if (!$stmt){
          die('Preparation Failed: ' .$conn -> error);
        }

        // Bind the PHP variables to the SQL placeholders
        // 'ssss' = 4 string values (email, recovery_em, username, password hash)
        $stmt -> bind_param('ssss',$email, $recovery_em,$username, $hash);

      try { 
        // Execute the prepared statement with the bound values
        $stmt -> execute();
        // Redirect to login page after successful registration
        header('Location: ');
      }catch(mysqli_sql_exception $err) { 
        // Catch any database errors during execution
        // Error code 1062 = duplicate entry (email already exists)
        if($err -> getCode() == 1062){
          echo "Email already signed up!";
        } else {
          // Generic error for other database issues
          echo "An Error Occurred, Please Try Again!";
        }
      }
    }
  }
}
// Close the database connection
mysqli_close($conn);
?>  

