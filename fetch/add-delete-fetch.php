<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../config/database.php');

if($_SERVER["REQUEST_METHOD"] == "POST") {

  // HANDLE DELETE REQUEST
  if(isset($_POST['delete'])) {
    $isbn = filter_input(INPUT_POST, "isbn", FILTER_SANITIZE_SPECIAL_CHARS);
    
    if(empty($isbn)) {
      echo "ISBN is required to delete a book!";
    } else {
      // CHECK IF THE BOOK EXISTS
      $check_sql = "SELECT book_title FROM books WHERE isbn = ?";
      $check_stmt = mysqli_prepare($conn, $check_sql);

      if($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "s", $isbn);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);

        if(mysqli_num_rows($result) > 0) {
          $row = mysqli_fetch_assoc($result);
          $book_title = $row['book_title'];

          // DELETE THE BOOK
          $delete_sql = "DELETE FROM books WHERE isbn = ?";
          $delete_stmt = mysqli_prepare($conn, $delete_sql);
          
          if($delete_stmt) {
            mysqli_stmt_bind_param($delete_stmt, "s", $isbn);
            
            if(mysqli_stmt_execute($delete_stmt)) {
              echo "Book '$book_title' deleted successfully!";
            } else {
              echo "Error deleting book: " . mysqli_stmt_error($delete_stmt);
            }
            
            mysqli_stmt_close($delete_stmt);
          }
        } else {
          echo "No book found with ISBN: $isbn";
        }
        
        mysqli_stmt_close($check_stmt);
      }
    };
  }
  
  // HANDLE ADD/REGISTER REQUEST
  elseif(isset($_POST['register'])) {
    $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_SPECIAL_CHARS);
    $author = filter_input(INPUT_POST, "author", FILTER_SANITIZE_SPECIAL_CHARS);
    $year_published = filter_input(INPUT_POST, "publish", FILTER_SANITIZE_NUMBER_INT);
    $book_type = filter_input(INPUT_POST, "bookType", FILTER_SANITIZE_SPECIAL_CHARS);
    $isbn = filter_input(INPUT_POST, "isbn", FILTER_SANITIZE_SPECIAL_CHARS);
    $quantity = filter_input(INPUT_POST, "quantity", FILTER_SANITIZE_NUMBER_INT);

    if(empty($title) || empty($author) || empty($year_published) || empty($book_type) || empty($isbn) || empty($quantity)) {
      echo "All fields are required!";
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
}
?>