  <?php
  
    include_once("../config/database.php");

  if($_SERVER["REQUEST_METHOD"] == "POST") { // Check if the method is post if yes
    // Variable for user
    $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
    // Variable for username
    $username = filter_input(INPUT_POST, "username", filter: FILTER_SANITIZE_SPECIAL_CHARS);
    // Variable for recovery password
    $recovery_em = filter_input(INPUT_POST, "recoveryEmail");
    // Variable for password
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirmPassword"] ?? "";

    if(!$email || !$recovery_em) {  // If email is missing
      echo "Email or Recovery Emailnis Missing!";
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
        $sql = "INSERT INTO users (email,recovery_em,username,password)
        VALUES (?,?,?,?)";

        // Preparing to send the SQL without the real data
        $stmt = $conn -> prepare($sql);
        
        // If preparation failed
        if (!$stmt){
          die('Preparation Failed: ' .$conn -> error);
        }

        // Attach the real PHP variable s stands for string
        $stmt -> bind_param('ssss',$email, $recovery_em,$username, $hash);

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

