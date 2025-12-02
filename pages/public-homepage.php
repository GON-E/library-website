<?php
// pages/public-homepage.php

// Start session first
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('../config/database.php');

// Check if user is logged in
$isLoggedIn = isset($_SESSION['userId']) && !empty($_SESSION['userId']);

// Handle borrow confirmation
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_borrow'])) {
    
    if(!$isLoggedIn) {
        $_SESSION['error_message'] = "Please log in to borrow books.";
        header("Location: user-login.php");
        exit();
    }
    
    $user_id = $_SESSION['userId'];
    $book_isbn = filter_input(INPUT_POST, "book_isbn", FILTER_SANITIZE_SPECIAL_CHARS);
    
    // Get book details
    $check_sql = "SELECT bookId, quantity, book_title FROM books WHERE isbn = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    
    if($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "s", $book_isbn);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        
        if($row = mysqli_fetch_assoc($result)) {
            $book_id = $row['bookId'];
            $quantity = $row['quantity'];
            $book_title = $row['book_title'];
            
            // Check availability
            if($quantity <= 0) {
                $_SESSION['error_message'] = "Sorry, this book is currently out of stock.";
            } else {
                // Check if already borrowed
                $check_borrowed_sql = "SELECT borrow_id FROM borrow_records 
                                       WHERE user_id = ? AND book_id = ? AND status = 'borrowed'";
                $check_borrowed_stmt = mysqli_prepare($conn, $check_borrowed_sql);
                mysqli_stmt_bind_param($check_borrowed_stmt, "ii", $user_id, $book_id);
                mysqli_stmt_execute($check_borrowed_stmt);
                $borrowed_result = mysqli_stmt_get_result($check_borrowed_stmt);
                
                if(mysqli_num_rows($borrowed_result) > 0) {
                    $_SESSION['error_message'] = "You have already borrowed this book.";
                } else {
                    // Set dates - 7 DAYS borrowing period
                    $date_borrowed = date('Y-m-d');
                    $due_date = date('Y-m-d', strtotime('+7 days'));
                    
                    // Insert borrow record
                    $insert_sql = "INSERT INTO borrow_records (user_id, book_id, date_borrowed, due_date, status) 
                                  VALUES (?, ?, ?, ?, 'borrowed')";
                    $insert_stmt = mysqli_prepare($conn, $insert_sql);
                    
                    if($insert_stmt) {
                        mysqli_stmt_bind_param($insert_stmt, "iiss", $user_id, $book_id, $date_borrowed, $due_date);
                        
                        if(mysqli_stmt_execute($insert_stmt)) {
                            // Decrease quantity
                            $update_sql = "UPDATE books SET quantity = quantity - 1 WHERE bookId = ?";
                            $update_stmt = mysqli_prepare($conn, $update_sql);
                            mysqli_stmt_bind_param($update_stmt, "i", $book_id);
                            mysqli_stmt_execute($update_stmt);
                            mysqli_stmt_close($update_stmt);
                            
                            $_SESSION['success_message'] = "Book borrowed successfully!";
                        }
                        mysqli_stmt_close($insert_stmt);
                    }
                }
                mysqli_stmt_close($check_borrowed_stmt);
            }
        }
        mysqli_stmt_close($check_stmt);
    }
    
    header("Location: " . $_SERVER['PHP_SELF'] . (isset($_GET['category']) ? '?category=' . $_GET['category'] : ''));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Browse Books - Lé Bros Library</title>
  <link rel="stylesheet" href="../styles/admin-homepage.css">
  <link rel="stylesheet" href="../styles/public-homepage.css">
</head>
<body>

  <header>
    <?php include('public-header.php'); ?>
  </header>

  <section>
    <?php include('public-nav.php'); ?>
  </section>

  <section>
    <?php include('book-category.php'); ?>
  </section>

  <!-- Success/Error Messages -->
  <?php if(isset($_SESSION['success_message'])): ?>
    <div style="background-color: #4CAF50; color: white; padding: 15px; text-align: center; margin: 20px auto; max-width: 600px; border-radius: 5px;">
      <?php 
        echo $_SESSION['success_message']; 
        unset($_SESSION['success_message']);
      ?>
    </div>
  <?php endif; ?>

  <?php if(isset($_SESSION['error_message'])): ?>
    <div style="background-color: #f44336; color: white; padding: 15px; text-align: center; margin: 20px auto; max-width: 600px; border-radius: 5px;">
      <?php 
        echo $_SESSION['error_message']; 
        unset($_SESSION['error_message']);
      ?>
    </div>
  <?php endif; ?>

  <!-- Login Required Modal -->
  <div id="loginModal" class="login-required-overlay">
    <div class="login-prompt">
      <h2>Login Required</h2>
      <p>You need to be logged in to borrow books.</p>
      <div class="login-prompt-buttons">
        <a href="user-login.php" class="btn-login-redirect">Login</a>
        <a href="user-signup.php" class="btn-signup-redirect">Sign Up</a>
        <button onclick="closeLoginModal()" class="btn-cancel">Cancel</button>
      </div>
    </div>
  </div>

  <!-- Borrow Confirmation Modal -->
  <div id="borrowModal" class="borrow-modal-overlay">
    <div class="borrow-modal">
      <button class="modal-close" onclick="closeBorrowModal()">&times;</button>
      
      <div class="modal-header">
        <h2>📚 Confirm Book Borrowing</h2>
      </div>
      
      <div class="modal-body">
        <div class="book-info-row">
          <span class="book-info-label">Book Title:</span>
          <span class="book-info-value" id="modal-book-title"></span>
        </div>
        
        <div class="book-info-row">
          <span class="book-info-label">Author:</span>
          <span class="book-info-value" id="modal-author"></span>
        </div>
        
        <div class="book-info-row">
          <span class="book-info-label">Category:</span>
          <span class="book-info-value" id="modal-category"></span>
        </div>
        
        <div class="book-info-row">
          <span class="book-info-label">ISBN:</span>
          <span class="book-info-value" id="modal-isbn"></span>
        </div>
        
        <div class="date-info">
          <div class="date-row">
            <span class="date-label">📅 Date Borrowed:</span>
            <span class="date-value" id="modal-date-borrowed"></span>
          </div>
          <div class="date-row">
            <span class="date-label">📆 Due Date:</span>
            <span class="date-value due-date" id="modal-due-date"></span>
          </div>
          <p style="font-size: 12px; color: #666; margin-top: 10px; text-align: center;">
            ⏰ You have <strong>7 days</strong> to return this book
          </p>
        </div>
      </div>
      
      <form method="post" id="confirmBorrowForm">
        <input type="hidden" name="book_isbn" id="confirm-book-isbn">
        <div class="modal-footer">
          <button type="submit" name="confirm_borrow" class="btn-confirm">
            ✓ Confirm Borrow
          </button>
          <button type="button" onclick="closeBorrowModal()" class="btn-cancel-modal">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>

  <section class="book-catalog-container">
    <section class="book-catalog">
      <?php
      if(isset($_GET['category'])) {
          $cat_filter = $_GET['category'];
          $sql = "SELECT * FROM books WHERE book_category = ? ORDER BY book_title ASC";
          $stmt = mysqli_prepare($conn, $sql);
          mysqli_stmt_bind_param($stmt, "s", $cat_filter);
          mysqli_stmt_execute($stmt);
          $result = mysqli_stmt_get_result($stmt);
      } 
      else {
          $sql = "SELECT * FROM books ORDER BY book_title ASC";
          $result = mysqli_query($conn, $sql);
      }

      if(mysqli_num_rows($result) > 0) {
          while($book = mysqli_fetch_assoc($result)) {
              ?>
              <section class="book-info-container">
                
                <div class="category-badge">
                    <?php echo htmlspecialchars($book['book_category']); ?>
                </div>

                <section class="image-container">
                  <?php if(!empty($book['image'])): ?>
                    <img src="../images/books/<?php echo htmlspecialchars($book['image']); ?>" 
                         alt="<?php echo htmlspecialchars($book['book_title']); ?>">
                  <?php else: ?>
                    <div class="no-image">No Image</div>
                  <?php endif; ?>
                </section>
                
                <section class="book-details">
                    <div class="book-title"><?php echo htmlspecialchars($book['book_title']); ?></div>
                    <div class="author-name"><?php echo htmlspecialchars($book['author']); ?></div>
                    
                    <div class="book-stock <?php echo ($book['quantity'] <= 0) ? 'out-of-stock' : ''; ?>">
                        Stock: <strong><?php echo $book['quantity']; ?></strong>
                        <?php if($book['quantity'] <= 0): ?>
                            <span class="stock-label">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                    
                    <button type="button" 
                            class="borrow-btn" 
                            <?php echo ($book['quantity'] <= 0) ? 'disabled' : ''; ?>
                            onclick='openBorrowModal(<?php echo json_encode([
                                "title" => $book["book_title"],
                                "author" => $book["author"],
                                "category" => $book["book_category"],
                                "isbn" => $book["isbn"]
                            ]); ?>)'>
                        <?php echo ($book['quantity'] > 0) ? 'Borrow Book' : 'Out of Stock'; ?>
                    </button>
                </section>

              </section>
              <?php
          }
      } else {
          echo "<p style='color:white; grid-column: 1/-1; text-align:center;'>No books found in this category.</p>";
      }
      
      if(isset($stmt)) { mysqli_stmt_close($stmt); }
      else { mysqli_free_result($result); }
      ?>
    </section>
  </section>

  <script>
    // Pass PHP variable to JavaScript
    const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
  </script>
  <script src="../script/borrow-modal.js"></script>

</body>

<section>
    <?php include('footer.php'); ?>
</section>

</html>