<?php
// pages/user-homepage.php - BROWSE & BORROW BOOKS (Logged In Users)
session_start();
include('../config/database.php');

// Check if user is logged in
if(!isset($_SESSION['userId'])) {
    header("Location: ../pages/user-login.php");
    exit();
}

$user_id = $_SESSION['userId'];

// Handle borrow book action
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrow_book'])) {
    $book_isbn = filter_input(INPUT_POST, "book_isbn", FILTER_SANITIZE_SPECIAL_CHARS);
    $borrow_duration_raw = filter_input(INPUT_POST, "borrow_duration", FILTER_SANITIZE_STRING);
    // Convert to float to handle fractional days (e.g., 0.0007 for 1 minute)
    $borrow_days = floatval($borrow_duration_raw) ?: 7;
    
    if(!empty($book_isbn)) {
        // Get book details using ISBN
        $check_sql = "SELECT bookId, isbn, quantity, book_title FROM books WHERE isbn = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        
        if($check_stmt) {
            mysqli_stmt_bind_param($check_stmt, "s", $book_isbn);
            mysqli_stmt_execute($check_stmt);
            $result = mysqli_stmt_get_result($check_stmt);
            
            if($row = mysqli_fetch_assoc($result)) {
                $book_id = $row['bookId'];
                $quantity = $row['quantity'];
                $book_title = $row['book_title'];
                
                // Check if book is available
                if($quantity <= 0) {
                    $_SESSION['error_message'] = "Sorry, this book is currently out of stock.";
                    mysqli_stmt_close($check_stmt);
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }
                
                // Check if user already has this book borrowed
                $check_borrowed_sql = "SELECT borrow_id FROM borrow_records 
                                       WHERE user_id = ? AND book_id = ? AND status = 'borrowed'";
                $check_borrowed_stmt = mysqli_prepare($conn, $check_borrowed_sql);
                mysqli_stmt_bind_param($check_borrowed_stmt, "ii", $user_id, $book_id);
                mysqli_stmt_execute($check_borrowed_stmt);
                $borrowed_result = mysqli_stmt_get_result($check_borrowed_stmt);
                
                if(mysqli_num_rows($borrowed_result) > 0) {
                    $_SESSION['error_message'] = "You have already borrowed this book.";
                    mysqli_stmt_close($check_borrowed_stmt);
                    mysqli_stmt_close($check_stmt);
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }
                mysqli_stmt_close($check_borrowed_stmt);
                
                // Set timezone to Manila (Asia/Manila) to ensure correct date
                date_default_timezone_set('Asia/Manila');
                
                // Set dates - borrow period based on user selection
                // Handle fractional days by converting to seconds
                $date_borrowed = date('Y-m-d H:i:s');
                $seconds_to_add = $borrow_days * 24 * 60 * 60;
                $due_date = date('Y-m-d H:i:s', time() + $seconds_to_add);
                
                // Insert borrow record
                $insert_sql = "INSERT INTO borrow_records (user_id, book_id, date_borrowed, due_date, status) 
                              VALUES (?, ?, ?, ?, 'borrowed')";
                $insert_stmt = mysqli_prepare($conn, $insert_sql);
                
                if($insert_stmt) {
                    mysqli_stmt_bind_param($insert_stmt, "iiss", $user_id, $book_id, $date_borrowed, $due_date);
                    
                    if(mysqli_stmt_execute($insert_stmt)) {
                        // Decrease book quantity
                        $update_sql = "UPDATE books SET quantity = quantity - 1 WHERE bookId = ?";
                        $update_stmt = mysqli_prepare($conn, $update_sql);
                        mysqli_stmt_bind_param($update_stmt, "i", $book_id);
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
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Browse Books - Lé Bros Library</title>
  <link rel="stylesheet" href="../styles/user-homepage.css">
  <link rel="icon" href="../images/icons/bookIcon.png" type="image/png">
</head>
<body>

  <header>
    <?php include('user-header.php'); ?>
  </header>

  <section>
    <?php include('user-nav.php'); ?>
  </section>

  <section>
    <?php include('book-category.php'); ?>
  </section>

  <?php if(isset($_SESSION['success_message'])): ?>
    <div class="success-message">
      <?php 
        echo $_SESSION['success_message']; 
        unset($_SESSION['success_message']);
      ?>
    </div>
  <?php endif; ?>

  <?php if(isset($_SESSION['error_message'])): ?>
    <div class="error-message">
      <?php 
        echo $_SESSION['error_message']; 
        unset($_SESSION['error_message']);
      ?>
    </div>
  <?php endif; ?>

  <!-- Borrow Confirmation Modal -->
  <div id="borrowModal" class="borrow-modal-overlay">
    <div class="borrow-modal">
      <button class="modal-close" onclick="closeBorrowModal()">&times;</button>
      
      <div class="modal-header">
        <h2>Confirm Book Borrowing</h2>
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
            <span class="date-label">Date Borrowed:</span>
            <span class="date-value" id="modal-date-borrowed"></span>
          </div>
          
          <div class="date-row">
            <span class="date-label">Due Date:</span>
            <span class="date-value due-date" id="modal-due-date"></span>
          </div>
        </div>

        <!-- Duration selection -->
        <div class="duration-selection">
          <label for="borrow-duration"><strong>Borrow Duration:</strong></label>
          <div class="duration-options">
            <label class="duration-option">
              <input type="radio" name="duration" value="0.0007" onchange="updateDueDate()">
              <span>1 Minute (Test)</span>
            </label>
            <label class="duration-option">
              <input type="radio" name="duration" value="1" onchange="updateDueDate()">
              <span>1 Day</span>
            </label>
            <label class="duration-option">
              <input type="radio" name="duration" value="3" onchange="updateDueDate()">
              <span>3 Days</span>
            </label>
            <label class="duration-option">
              <input type="radio" name="duration" value="7" checked onchange="updateDueDate()">
              <span>7 Days</span>
            </label>
            <label class="duration-option">
              <input type="radio" name="duration" value="14" onchange="updateDueDate()">
              <span>14 Days</span>
            </label>
            <label class="duration-option">
              <input type="radio" name="duration" value="21" onchange="updateDueDate()">
              <span>21 Days</span>
            </label>
          </div>
        </div>
      </div>
      
      <form method="post" class="modal-footer">
        <input type="hidden" name="book_isbn" id="confirm-book-isbn">
        <input type="hidden" name="borrow_duration" id="confirm-duration" value="7">
        <button type="submit" name="borrow_book" class="btn-confirm">Confirm Borrow</button>
        <button type="button" onclick="closeBorrowModal()" class="btn-cancel-modal">Cancel</button>
      </form>
    </div>
  </div>

  <section class="book-catalog-container">
    <section class="book-catalog">
      <?php
      // Filter books by category
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

  <script src="../script/user-homepage.js"></script>

</body>

<section>
    <?php include('footer.php'); ?>
</section>

</html>