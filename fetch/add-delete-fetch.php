<?php
// Ensure this is include_once to prevent double-loading
include_once('../config/database.php');

// DEFINE THE UPLOAD DIR ABSOLUTE PATH
// __DIR__ gets the folder of THIS file (fetch/). 
// dirname(__DIR__) goes up one level to the root.
define('UPLOAD_DIR', dirname(__DIR__) . '/images/books/');

if($_SERVER["REQUEST_METHOD"] == "POST") {

  // --- HANDLE DELETE REQUEST ---
  if(isset($_POST['delete_btn'])) {
    $isbn = filter_input(INPUT_POST, "isbn", FILTER_SANITIZE_SPECIAL_CHARS);
    
    if(empty($isbn)) {
      echo "ISBN is required to delete a book!";
    } else {
      $check_sql = "SELECT book_title, image FROM books WHERE isbn = ?";
      $check_stmt = mysqli_prepare($conn, $check_sql);

      if($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "s", $isbn);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);

        if(mysqli_num_rows($result) > 0) {
          $row = mysqli_fetch_assoc($result);
          // TRIM whitespace to ensure filename matches exactly
          $image = trim($row['image']); 

          // DELETE FROM DATABASE FIRST
          $delete_sql = "DELETE FROM books WHERE isbn = ?";
          $delete_stmt = mysqli_prepare($conn, $delete_sql);
          
          if($delete_stmt) {
            mysqli_stmt_bind_param($delete_stmt, "s", $isbn);
            
            if(mysqli_stmt_execute($delete_stmt)) {
               // SUCCESS: NOW DELETE THE FILE
               // Use the absolute path defined above
               $full_path = UPLOAD_DIR . $image;

               if(!empty($image) && file_exists($full_path)) {
                  if(unlink($full_path)) {
                      // File deleted
                  } else {
                      // Permission error or file locked
                  }
               }
               
               // PREVENT RESUBMISSION: Redirect to self
               header("Location: " . $_SERVER['PHP_SELF']);
               exit();

            } else {
               echo "Error deleting book from DB: " . mysqli_stmt_error($delete_stmt);
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
  
  // --- HANDLE ADD/REGISTER REQUEST ---
  elseif(isset($_POST['register'])) {
    $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_SPECIAL_CHARS);
    $author = filter_input(INPUT_POST, "author", FILTER_SANITIZE_SPECIAL_CHARS);
    $year_published = filter_input(INPUT_POST, "publish", FILTER_SANITIZE_SPECIAL_CHARS);
    $book_type = filter_input(INPUT_POST, "bookType", FILTER_SANITIZE_SPECIAL_CHARS);
    $isbn = filter_input(INPUT_POST, "isbn", FILTER_SANITIZE_SPECIAL_CHARS);
    $quantity = filter_input(INPUT_POST, "quantity", FILTER_SANITIZE_NUMBER_INT);

    if(empty($title) || empty($author) || empty($year_published) || empty($book_type) || empty($isbn) || empty($quantity)) {
      echo "All fields are required!";
    } else {

      $imageName = null;
      $uploadSuccess = true;

      // HANDLE IMAGE UPLOAD
      if(!empty($_FILES["bookImage"]["name"])) {
        $tmp_name = $_FILES["bookImage"]["tmp_name"];
        $original_name = $_FILES["bookImage"]["name"];
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        
        // Generate unique name
        $imageName = "book_" . time() . "_" . rand(1000, 9999) . "." . $file_extension;
        
        // Use the Absolute Path
        $upload_path = UPLOAD_DIR . $imageName;

        if(!move_uploaded_file($tmp_name, $upload_path)) {
           echo "Error uploading image. Check folder permissions.";
           $uploadSuccess = false;
        }
      }

      if($uploadSuccess) {
          $sql = "INSERT INTO books (book_title, author, year_published, book_category, isbn, quantity, image) VALUES (?,?,?,?,?,?,?)";
          $statement = mysqli_prepare($conn, $sql);
          
          if($statement) {
            try {
              mysqli_stmt_bind_param($statement, "sssssis",
                $title, $author, $year_published, $book_type, $isbn, $quantity, $imageName
              );
              
              if(mysqli_stmt_execute($statement)) {
                // SUCCESS! 
                // PREVENT DUPLICATES: Redirect to self (Refresh the page cleanly)
                header("Location: " . $_SERVER['PHP_SELF']);
                exit(); 

              } else {
                echo "Error executing query: " . mysqli_stmt_error($statement);
                // Optional: If DB insert fails, delete the uploaded image so it's not orphaned
                if($imageName && file_exists(UPLOAD_DIR . $imageName)) {
                    unlink(UPLOAD_DIR . $imageName);
                }
              }
              mysqli_stmt_close($statement);            
            } catch(mysqli_sql_exception $e) {
              echo "Error: " . $e->getMessage();
            }
          }
      }
    }
  }
}
?>