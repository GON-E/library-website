<?php
include('../config/database.php');




if($_SERVER["REQUEST_METHOD"] == "POST") {

  //WILL HANDEL THE DELETE REQUEST 

  $isbn = filter_input(INPUT_POST, "isbn", FILTER_SANITIZE_SPECIAL_CHARS);
  if(empty($isbn)){
    echo"ISBN is required to delete a book!"

  }else{
 // IT WILL CHECK FIRST THE BOOK EXIST
  $check_sql = "SELECT book_title FROM Books WHERE isbn = ?";
  $check_stmt = mysqli_prepare($conn, $check_sql);

  if($check_stmt){
    mysqli_stmt_bind_param( $check_stmt,"s", $isbn);
    mysqli_stmt_execute(check_stmt);
  }

  }

  $author = filter_input(INPUT_POST, "author", FILTER_SANITIZE_SPECIAL_CHARS);
  $year_published = filter_input(INPUT_POST, "publish", FILTER_SANITIZE_NUMBER_INT);
  $book_type = filter_input(INPUT_POST, "bookType", FILTER_SANITIZE_SPECIAL_CHARS);
  $isbn = filter_input(INPUT_POST, "isbn", FILTER_SANITIZE_SPECIAL_CHARS);
  $quantity = filter_input(INPUT_POST, "quantity", FILTER_SANITIZE_NUMBER_INT);

  if(empty($title) || empty($author) || empty($year_published) || empty($book_type) || empty($isbn) || empty($quantity)) {
    echo "All fields must be required!";
  } else {
      $sql = "INSERT INTO books (book_title, author, year_published, book_category, isbn, quantity) VALUES (?,?,?,?,?,?)";
      $statement = mysqli_prepare($conn, $sql);
      
      if($statement) {
        try {
          mysqli_stmt_bind_param($statement, "ssisii",
            $title,
            $author,
            $year_published,
            $book_type,
            $isbn,
            $quantity
          );
          
          if(mysqli_stmt_execute($statement)) {
            echo "Book added successfully!";
          } else {
            echo "Error executing query: " . mysqli_stmt_error($statement);
          }
          
          mysqli_stmt_close($statement);            
          
        } catch(mysqli_sql_exception $e) {
          echo "Error occurred: " . $e->getMessage();
        }
      } else {
        echo "Error preparing statement: " . mysqli_error($conn);
      }
  }
}
?>