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
  <link rel="stylesheet" href="../styles/add-book.css">
  <link rel="icon" href="../images/favicon.jpg" type="image/x-icon">
</head>
<body>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
  <section>
    <h2>Add Book</h2>
    Book Title:
    <input type="text" name="title"> <br>
    Author:
    <input type="text" name="author"><br>
    Year Published: 
    <input type="text" name="publish"> <br>
    Book Type:
    <input type="text" name="bookType"> <br>
    ISBN:
    <input type="text" name="isbn"> <br>
    Quantity:
    <input type="text" name="quantity"> <br>
    <input type="submit" name="register" value="register">
  </section>
  <section>
    <h2 id="delete">Delete Book</h2>
    <input type="text" name="delete" value="delete">
    <input type="submit" name="delete" value="delete">
  </section>
  </form>
</body>
</html>

<!-- ->
