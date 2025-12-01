<?php
// Start session and check if user is logged in
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not logged in
if (!isset($_SESSION['userId']) || empty($_SESSION['userId'])) {
    header("Location: user-login.php");
    exit();
}

include('../config/database.php');

$userId = $_SESSION['userId'];
$userName = $_SESSION['userName'];

// Update overdue status
$today = date('Y-m-d');
$update_overdue = "UPDATE borrowed_books 
                   SET status = 'overdue' 
                   WHERE user_id = ? 
                   AND status = 'borrowed' 
                   AND return_date < ?";
$stmt_overdue = mysqli_prepare($conn, $update_overdue);
mysqli_stmt_bind_param($stmt_overdue, "is", $userId, $today);
mysqli_stmt_execute($stmt_overdue);
mysqli_stmt_close($stmt_overdue);

// --- HANDLE RETURN BOOK REQUEST ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return_book'])) {
    $borrow_id = $_POST['borrow_id'];
    $book_isbn = $_POST['book_isbn'];
    
    // Update borrowed_books table
    $return_sql = "UPDATE borrowed_books 
                   SET status = 'returned', actual_return_date = ? 
                   WHERE borrow_id = ? AND user_id = ?";
    $return_stmt = mysqli_prepare($conn, $return_sql);
    mysqli_stmt_bind_param($return_stmt, "sii", $today, $borrow_id, $userId);
    
    if (mysqli_stmt_execute($return_stmt)) {
        // Increase book quantity back
        $update_book = "UPDATE books SET quantity = quantity + 1 WHERE isbn = ?";
        $update_stmt = mysqli_prepare($conn, $update_book);
        mysqli_stmt_bind_param($update_stmt, "s", $book_isbn);
        mysqli_stmt_execute($update_stmt);
        mysqli_stmt_close($update_stmt);
        
        echo "<script>
            alert('Book returned successfully! Thank you.');
            window.location.href = '" . $_SERVER['PHP_SELF'] . "';
        </script>";
        exit();
    }
    mysqli_stmt_close($return_stmt);
}

// Get borrowed books with their images
$borrowed_sql = "SELECT bb.*, b.image 
                 FROM borrowed_books bb
                 LEFT JOIN books b ON bb.book_isbn = b.isbn
                 WHERE bb.user_id = ? 
                 AND (bb.status = 'borrowed' OR bb.status = 'overdue')
                 ORDER BY bb.borrow_date DESC";
$borrowed_stmt = mysqli_prepare($conn, $borrowed_sql);
mysqli_stmt_bind_param($borrowed_stmt, "i", $userId);
mysqli_stmt_execute($borrowed_stmt);
$borrowed_result = mysqli_stmt_get_result($borrowed_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Borrowed Books</title>
  <link rel="stylesheet" href="../styles/user-homepage.css">
</head>
<body>
  
  <header><?php include('user-header.php'); ?></header>
  <section><?php include('user-nav.php'); ?></section>

  <section class="book-catalog-container">
    
    <section class="page-title">
      <h1>📚 My Borrowed Books</h1>
      <p>Here are the books you currently have</p>
    </section>

    <section class="book-catalog">
      <?php
      if(mysqli_num_rows($borrowed_result) > 0) {
          while($book = mysqli_fetch_assoc($borrowed_result)) {
              // Calculate days left
              $return_date = new DateTime($book['return_date']);
              $today_date = new DateTime($today);
              $days_left = $today_date->diff($return_date)->days;
              $is_overdue = $book['status'] == 'overdue';
              ?>
              <section class="book-info-container <?php echo $is_overdue ? 'overdue-book' : ''; ?>">
                
                <!-- Status Badge (Top) -->
                <div class="status-badge-top <?php echo $book['status']; ?>">
                    <?php echo $is_overdue ? '⚠️ OVERDUE' : '✓ BORROWED'; ?>
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
                    
                    <!-- Borrow Information -->
                    <div class="borrow-info">
                        <div class="info-row">
                            <span class="info-label">📅 Borrowed:</span>
                            <span class="info-value"><?php echo date('M d, Y', strtotime($book['borrow_date'])); ?></span>
                        </div>
                        <div class="info-row return-date-row">
                            <span class="info-label">⏰ Return by:</span>
                            <span class="info-value"><?php echo date('M d, Y', strtotime($book['return_date'])); ?></span>
                        </div>
                        <div class="info-row days-left-row">
                            <span class="info-label">⏳ Days Left:</span>
                            <span class="days-count <?php echo $is_overdue ? 'overdue' : ($days_left <= 1 ? 'urgent' : ''); ?>">
                                <?php 
                                if ($is_overdue) {
                                    echo $days_left . ' day' . ($days_left != 1 ? 's' : '') . ' overdue';
                                } else {
                                    echo $days_left . ' day' . ($days_left != 1 ? 's' : '');
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Return Button -->
                    <form method="post" class="return-form">
                        <input type="hidden" name="borrow_id" value="<?php echo $book['borrow_id']; ?>">
                        <input type="hidden" name="book_isbn" value="<?php echo $book['book_isbn']; ?>">
                        <button type="submit" 
                                name="return_book" 
                                class="return-btn"
                                onclick="return confirm('Are you sure you want to return this book?')">
                            📤 Return Book
                        </button>
                    </form>
                </section>

              </section>
              <?php
          }
      } else {
          ?>
          <div class="no-books-container">
              <div class="no-books-message">
                  <h2>📭 No Borrowed Books</h2>
                  <p>You haven't borrowed any books yet.</p>
                  <a href="public-homepage.php" class="browse-books-btn">
                      📚 Browse Books
                  </a>
              </div>
          </div>
          <?php
      }
      
      mysqli_stmt_close($borrowed_stmt);
      mysqli_close($conn);
      ?>
    </section>
  </section>

</body>
<section><?php include('footer.php'); ?></section>
</html>