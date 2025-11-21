<?php
  include('../config/database.php');
  include('../fetch/add-delete-fetch.php');
  
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Book</title>
  
  <link rel="icon" href="../images/favicon.jpg" type="image/x-icon">
</head>
<body>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
    Book Title:
    <input type="text" name="title"> <br>
    Author:
    <input type="text" name="author"> <br>
    Year Published: 
    <input type="text" name="publish"> <br>
    book_type
    <input type="text" name="bookType"> <br>
    isbn:
    <input type="text" name="isbn"> <br>
    quantity:
    <input type="text" name="quantity"> <br>

    <input type="submit" name="register" value="register">
    <input type="submit" name="delete" value="delete">
  </form>
</body>
</html>

<!-- ->
