<?php 
  include('../config/database.php');

  if($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_SPECIAL_CHARS);
    $author = filter_input(INPUT_POST, "author", FILTER_SANITIZE_SPECIAL_CHARS);
    $year_publish = filter_input(INPUT_POST, "publish", FILTER_SANITIZE_SPECIAL_CHARS);
    $book_type = filter_input(INPUT_POST, "bookType", FILTER_SANITIZE_SPECIAL_CHARS);
    $isbn = filter_input(INPUT_POST, "isbn", FILTER_SANITIZE_SPECIAL_CHARS);

    if(empty($title) || empty($author) || empty($publish) || empty($isbn)) {
      echo "All field must be required!";
    } else {
      echo "Successful Input";
      
      $sql = "INSERT INTO books (book_title, author, year_published, book_type, isbn) VALUES
        ('$title', '$author', '$year_publish', '$book_type', '$isbn')";
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
</head>
<body>
  <form>
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
    
    <input type="submit" name="register" value="register">
  </form>
</body>
</html>
