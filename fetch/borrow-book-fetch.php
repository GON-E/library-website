<?php
// fetch/borrow-book-fetch.php - Handles borrowing books (legacy, mostly replaced by modal in user-homepage.php)

// Check if form was submitted using POST method and borrow_book button was clicked
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrow_book'])) {
    
    // Check if user is logged in by checking if userId is in the session
    if(!isset($_SESSION['userId'])) {
        // User not logged in - redirect to signup page to register first
        header("Location: ../pages/user-signup.php");
        exit();
    }
    
    // Get the logged-in user's ID from the session
    $user_id = $_SESSION['userId'];
    // Get and clean the book ISBN from the form
    $book_isbn = filter_input(INPUT_POST, "book_isbn", FILTER_SANITIZE_SPECIAL_CHARS);
    
    // Check if ISBN is empty
    if(empty($book_isbn)) {
        $_SESSION['error_message'] = "Invalid book selection.";
        // Send user back to the page they came from
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // SQL query to fetch book details using ISBN (prepared statement for security)
    $check_sql = "SELECT bookId, isbn, quantity, book_title FROM books WHERE isbn = ?";
    // Prepare the SQL statement
    $check_stmt = mysqli_prepare($conn, $check_sql);
    
    // Check if preparation was successful
    if($check_stmt) {
        // Bind the ISBN variable to the placeholder
        mysqli_stmt_bind_param($check_stmt, "s", $book_isbn);
        // Execute the query
        mysqli_stmt_execute($check_stmt);
        // Get the result set
        $result = mysqli_stmt_get_result($check_stmt);
        
        // Check if a book was found with that ISBN
        if($row = mysqli_fetch_assoc($result)) {
            // Get the book ID from the result
            $book_id = $row['bookId'];
            // Get the available quantity
            $quantity = $row['quantity'];
            // Get the book title for display messages
            $book_title = $row['book_title'];
            
            // Check if the book is in stock
            if($quantity <= 0) {
                $_SESSION['error_message'] = "Sorry, this book is currently out of stock.";
                // Close the statement
                mysqli_stmt_close($check_stmt);
                // Send user back to previous page
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
            
            // SQL query to check if user already borrowed this book
            $check_borrowed_sql = "SELECT borrow_id FROM borrow_records 
                                   WHERE user_id = ? AND book_id = ? AND status = 'borrowed'";
            // Prepare the query
            $check_borrowed_stmt = mysqli_prepare($conn, $check_borrowed_sql);
            // Bind user_id and book_id to the placeholders (both integers: ii)
            mysqli_stmt_bind_param($check_borrowed_stmt, "ii", $user_id, $book_id);
            // Execute the query
            mysqli_stmt_execute($check_borrowed_stmt);
            // Get the result set
            $borrowed_result = mysqli_stmt_get_result($check_borrowed_stmt);
            
            // Check if user already has this book borrowed
            if(mysqli_num_rows($borrowed_result) > 0) {
                $_SESSION['error_message'] = "You have already borrowed this book.";
                // Close both statements
                mysqli_stmt_close($check_borrowed_stmt);
                mysqli_stmt_close($check_stmt);
                // Send user back to previous page
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
            // Close the borrowed check statement
            mysqli_stmt_close($check_borrowed_stmt);
            
            // Get today's date
            $date_borrowed = date('Y-m-d');
            // Calculate due date as 7 days from today
            $due_date = date('Y-m-d', strtotime('+7 days'));
            
            // SQL query to insert a new borrow record
            $insert_sql = "INSERT INTO borrow_records (user_id, book_id, date_borrowed, due_date, status) 
                          VALUES (?, ?, ?, ?, 'borrowed')";
            // Prepare the insert statement
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            
            // Check if preparation was successful
            if($insert_stmt) {
                // Bind values: 2 integers (user_id, book_id) and 2 strings (dates)
                mysqli_stmt_bind_param($insert_stmt, "iiss", $user_id, $book_id, $date_borrowed, $due_date);
                
                // Execute the insert
                if(mysqli_stmt_execute($insert_stmt)) {
                    // Borrow record created, now decrease the book quantity by 1
                    $update_sql = "UPDATE books SET quantity = quantity - 1 WHERE bookId = ?";
                    // Prepare the update statement
                    $update_stmt = mysqli_prepare($conn, $update_sql);
                    // Bind the book ID
                    mysqli_stmt_bind_param($update_stmt, "i", $book_id);
                    // Execute the update
                    mysqli_stmt_execute($update_stmt);
                    // Close the update statement
                    mysqli_stmt_close($update_stmt);
                    
                    // Set success message with formatted due date
                    $_SESSION['success_message'] = "Book borrowed successfully! Due date: " . date('F j, Y', strtotime($due_date));
                } else {
                    // If borrow insert failed, set error message
                    $_SESSION['error_message'] = "Error borrowing book. Please try again.";
                }
                // Close the insert statement
                mysqli_stmt_close($insert_stmt);
            }
        } else {
            // Book not found with that ISBN
            $_SESSION['error_message'] = "Book not found.";
        }
        // Close the book check statement
        mysqli_stmt_close($check_stmt);
    }
    
    // Send user back to the page they came from (homepage)
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>