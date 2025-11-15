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
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>"method="post">
    Username:
    <input type="text" name="username" placeholder="username"> <br>
    Password:
    <input type="text" name="username" placeholder="username"> <br>
    <input type="submit" name="submit";
    
  </form>
</body>
</html>

<?php 
  if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, $email, FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, $password, FILTER_SANITIZE_SPECIAL_CHARS);
    

    if(!$email) {
      echo "Email is missing";
    } else if(empty($password)) {
      echo "Password is missing";
    } else {

      $sql = 'SELECT * FROM users WHERE email = $email';
      mysqli_query($conn, $sql);


    }
    

  }

?>
