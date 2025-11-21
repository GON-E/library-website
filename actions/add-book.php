<?php

use Dom\Mysql;

  include('../config/database.php');
  if($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_SPECIAL_CHARS);
    $author = filter_input(INPUT_POST, "author", FILTER_SANITIZE_SPECIAL_CHARS);
    $year_published = filter_input(INPUT_POST, "publish", FILTER_SANITIZE_NUMBER_INT);
    $book_type = filter_input(INPUT_POST, "bookType", FILTER_SANITIZE_SPECIAL_CHARS);
    $isbn = filter_input(INPUT_POST, "isbn", FILTER_SANITIZE_SPECIAL_CHARS);
    $quantity = filter_input(INPUT_POST, "quantity", FILTER_SANITIZE_NUMBER_INT);

    if(empty($title) || empty($author) || empty($year_published) || empty($book_type) || empty($isbn) || empty($quantity)) {
      echo "All field must be required!";
    } else {
        echo "Successful Input";
      
        $sql = "INSERT INTO books (book_title, author, year_published, book_category, isbn, quantity) VALUES
        (?,?,?,?,?,?)";
        $statement = Mysqli

        try {
          $result = mysqli_query($conn, $sql);
        } catch(mysqli_sql_exception){ 
          echo "Error Occured";
        }
    };
  };
  
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
  </form>
</body>
</html>
