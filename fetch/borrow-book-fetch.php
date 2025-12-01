<?php
// fetch/borrow-book-fetch.php
include('../config/database.php');

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrow_book'])) {
    
    // Check if user is logged in
    if(!isset($_SESSION['userId'])) {
        header("Location: ../pages/user-login.php");
        exit();
    }
    
    $user_id = $_SESSION['userId'];
    $book_isbn = filter_input(INPUT_POST, "book_isbn", FILTER_SANITIZE_SPECIAL_CHARS);
    
    if(empty($book_isbn)) {
        $_SESSION['error_message'] = "Invalid book selection.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // Get book details and check availability
    // Note: Using isbn as the book identifier since there's no book_id column
    $check_sql = "SELECT isbn, quantity FROM books WHERE isbn = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    
    if($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "s", $book_isbn);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        
        if($row = mysqli_fetch_assoc($result)) {
            $book_id = $row['isbn']; // Using isbn as book_id
            $quantity = $row['quantity'];
            
            // Check if book is available
            if($quantity <= 0) {
                $_SESSION['error_message'] = "Sorry, this book is currently out of stock.";
                mysqli_stmt_close($check_stmt);
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
            
            // Check if user already has this book borrowed
            $check_borrowed_sql = "SELECT borrow_id FROM borrow_records 
                                   WHERE user_id = ? AND book_id = ? AND status = 'borrowed'";
            $check_borrowed_stmt = mysqli_prepare($conn, $check_borrowed_sql);
            mysqli_stmt_bind_param($check_borrowed_stmt, "is", $user_id, $book_id); // Changed to "is" since book_id is now isbn (string)
            mysqli_stmt_execute($check_borrowed_stmt);
            $borrowed_result = mysqli_stmt_get_result($check_borrowed_stmt);
            
            if(mysqli_num_rows($borrowed_result) > 0) {
                $_SESSION['error_message'] = "You have already borrowed this book.";
                mysqli_stmt_close($check_borrowed_stmt);
                mysqli_stmt_close($check_stmt);
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
            mysqli_stmt_close($check_borrowed_stmt);
            
            // Set dates
            $date_borrowed = date('Y-m-d');
            $due_date = date('Y-m-d', strtotime('+14 days')); // 14 days borrowing period
            
            // Insert borrow record
            $insert_sql = "INSERT INTO borrow_records (user_id, book_id, date_borrowed, due_date, status) 
                          VALUES (?, ?, ?, ?, 'borrowed')";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            
            if($insert_stmt) {
                mysqli_stmt_bind_param($insert_stmt, "isss", $user_id, $book_id, $date_borrowed, $due_date); // Changed to "isss"
                
                if(mysqli_stmt_execute($insert_stmt)) {
                    // Decrease book quantity - using isbn instead of book_id
                    $update_sql = "UPDATE books SET quantity = quantity - 1 WHERE isbn = ?";
                    $update_stmt = mysqli_prepare($conn, $update_sql);
                    mysqli_stmt_bind_param($update_stmt, "s", $book_id); // "s" for string (isbn)
                    mysqli_stmt_execute($update_stmt);
                    mysqli_stmt_close($update_stmt);
                    
                    $_SESSION['success_message'] = "Book borrowed successfully! Due date: " . date('F j, Y', strtotime($due_date));
                } else {
                    $_SESSION['error_message'] = "Error borrowing book. Please try again.";
                }
                mysqli_stmt_close($insert_stmt);
            }
        } else {
            $_SESSION['error_message'] = "Book not found.";
        }
        mysqli_stmt_close($check_stmt);
    }
    
    // Redirect back to homepage
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>