<?php
// fetch/add-delete-fetch.php - Handles adding new books to the database

// Include database connection only once to prevent conflicts
include_once('../config/database.php');

// Define the folder path where book images will be stored
// __DIR__ = current folder (fetch/)
// dirname(__DIR__) = go up one level to root folder
// Final path: /library-website/images/books/
define('UPLOAD_DIR', dirname(__DIR__) . '/images/books/');

// Check if the form was submitted using POST method
if($_SERVER["REQUEST_METHOD"] == "POST") {

  // Check if the 'register' button was clicked (adding a new book)
  if(isset($_POST['register'])) {
    // Get and clean the book title from the form
    $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_SPECIAL_CHARS);
    // Get and clean the author name from the form
    $author = filter_input(INPUT_POST, "author", FILTER_SANITIZE_SPECIAL_CHARS);
    // Get and clean the publish date from the form
    $year_published = filter_input(INPUT_POST, "publish", FILTER_SANITIZE_SPECIAL_CHARS);
    // Get and clean the book category from the form
    $book_type = filter_input(INPUT_POST, "bookType", FILTER_SANITIZE_SPECIAL_CHARS);
    // Get and clean the ISBN from the form
    $isbn = filter_input(INPUT_POST, "isbn", FILTER_SANITIZE_SPECIAL_CHARS);
    // Get and clean the quantity (must be a number)
    $quantity = filter_input(INPUT_POST, "quantity", FILTER_SANITIZE_NUMBER_INT);

    // Check if any required field is empty
    if(empty($title) || empty($author) || empty($year_published) || empty($book_type) || empty($isbn) || empty($quantity)) {
      echo "All fields are required!";
    } else {

      // Initialize the image name as null (no image yet)
      $imageName = null;
      // Flag to track if image upload was successful
      $uploadSuccess = true;

      // Check if an image file was uploaded
      if(!empty($_FILES["bookImage"]["name"])) {
        // Get the temporary location of uploaded file
        $tmp_name = $_FILES["bookImage"]["tmp_name"];
        // Get the original filename
        $original_name = $_FILES["bookImage"]["name"];
        // Extract the file extension (e.g., 'jpg', 'png')
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        
        // Create a unique filename using timestamp and random number to avoid conflicts
        $imageName = "book_" . time() . "_" . rand(1000, 9999) . "." . $file_extension;
        
        // Combine the upload folder path with the new filename
        $upload_path = UPLOAD_DIR . $imageName;

        // Try to move the uploaded file to the books folder
        if(!move_uploaded_file($tmp_name, $upload_path)) {
           echo "Error uploading image. Check folder permissions.";
           // Mark upload as failed
           $uploadSuccess = false;
        }
      }

      // If image upload was successful (or no image was provided), proceed to database insert
      if($uploadSuccess) {
          // SQL query to insert book data into the books table
          $sql = "INSERT INTO books (book_title, author, year_published, book_category, isbn, quantity, image) VALUES (?,?,?,?,?,?,?)";
          // Prepare the SQL statement to prevent SQL injection
          $statement = mysqli_prepare($conn, $sql);
          
          // Check if the statement was prepared successfully
          if($statement) {
            try {
              // Bind the PHP variables to the SQL placeholders
              // 's' = string, 'i' = integer, 's' = string (sssssis = 7 values: 6 strings and 1 integer)
              mysqli_stmt_bind_param($statement, "sssssis",
                $title, $author, $year_published, $book_type, $isbn, $quantity, $imageName
              );
              
              // Execute the prepared statement with the actual values
              if(mysqli_stmt_execute($statement)) {
                // SUCCESS! Book was added to database
                // Redirect to self to prevent form resubmission when page is refreshed
                header("Location: " . $_SERVER['PHP_SELF']);
                exit(); 

              } else {
                echo "Error executing query: " . mysqli_stmt_error($statement);
                // If database insert fails, delete the uploaded image file so it's not orphaned
                if($imageName && file_exists(UPLOAD_DIR . $imageName)) {
                    unlink(UPLOAD_DIR . $imageName);
                }
              }
              // Close the prepared statement
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