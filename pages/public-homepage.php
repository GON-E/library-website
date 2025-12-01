<?php
// Start session to check if user is logged in
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('../config/database.php');

// --- HANDLE BORROW REQUEST ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrow_book'])) {
    // Check if user is logged in
    if (!isset($_SESSION['userId']) || empty($_SESSION['userId'])) {
        // User is NOT logged in - show alert and redirect
        echo "<script>
            alert('Please log in first to borrow books!');
            window.location.href = 'user-login.php';
        </script>";
        exit();
    } else {
        // User IS logged in - process the borrow request
        $isbn = $_POST['book_isbn'];
        $userId = $_SESSION['userId'];
        
        // Calculate dates
        $borrowDate = date('Y-m-d'); // Today
        $returnDate = date('Y-m-d', strtotime('+3 days')); // 3 days from today
        
        // Check if book has stock
        $check_sql = "SELECT quantity, book_title FROM books WHERE isbn = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $isbn);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if ($row['quantity'] > 0) {
                // Get book details
                $book_title = $row['book_title'];
                
                // Book is available - decrease quantity
                $update_sql = "UPDATE books SET quantity = quantity - 1 WHERE isbn = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "s", $isbn);
                
                if (mysqli_stmt_execute($update_stmt)) {
                    // Record the borrow transaction
                    $insert_borrow = "INSERT INTO borrowed_books 
                                     (user_id, book_isbn, book_title, author, book_category, borrow_date, return_date, status) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, 'borrowed')";
                    $borrow_stmt = mysqli_prepare($conn, $insert_borrow);
                    
                    // Get book details for the record
                    $book_title = $row['book_title'];
                    $book_sql = "SELECT author, book_category FROM books WHERE isbn = ?";
                    $book_detail_stmt = mysqli_prepare($conn, $book_sql);
                    mysqli_stmt_bind_param($book_detail_stmt, "s", $isbn);
                    mysqli_stmt_execute($book_detail_stmt);
                    $book_result = mysqli_stmt_get_result($book_detail_stmt);
                    $book_data = mysqli_fetch_assoc($book_result);
                    
                    mysqli_stmt_bind_param($borrow_stmt, "issssss", 
                        $userId, $isbn, $book_title, 
                        $book_data['author'], $book_data['book_category'], 
                        $borrowDate, $returnDate
                    );
                    mysqli_stmt_execute($borrow_stmt);
                    mysqli_stmt_close($borrow_stmt);
                    mysqli_stmt_close($book_detail_stmt);
                    
                    echo "<script>
                        alert('Book borrowed successfully!\\n\\nBorrow Date: " . date('F d, Y') . "\\nReturn Date: " . date('F d, Y', strtotime('+3 days')) . "\\n\\nPlease return the book on time.');
                        window.location.href = '" . $_SERVER['PHP_SELF'] . (isset($_GET['category']) ? "?category=" . $_GET['category'] : "") . "';
                    </script>";
                    exit();
                } else {
                    echo "<script>alert('Error borrowing book. Please try again.');</script>";
                }
                mysqli_stmt_close($update_stmt);
            } else {
                echo "<script>alert('Sorry, this book is currently out of stock.');</script>";
            }
        }
        mysqli_stmt_close($check_stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Public Homepage</title>
  <link rel="stylesheet" href="../styles/public-homepage.css">
</head>
<body>

  <header>
    <?php 
    // Check if user is logged in - if yes, use user-header, else public-header
    if ($userLoggedIn) {
        include('user-header.php');
    } else {
        include('public-header.php');
    }
    ?>
  </header>

  <section>
    <?php 
    // Check if user is logged in - if yes, use user-nav, else public-nav
    if ($userLoggedIn) {
        include('user-nav.php');
    } else {
        include('public-nav.php');
    }
    ?>
  </section>

  <section>
    <?php include('book-category.php'); ?>
  </section>

  <section class="book-catalog-container">
    
    <section class="book-catalog">
      <?php
      // --- LOGIC TO FILTER BOOKS ---
      
      // Check if user is logged in for display purposes
      $userLoggedIn = isset($_SESSION['userId']) && !empty($_SESSION['userId']);
      
      // 1. Check if a category link was clicked
      if(isset($_GET['category'])) {
          $cat_filter = $_GET['category'];
          
          // Prepared statement for security
          $sql = "SELECT * FROM books WHERE book_category = ? ORDER BY book_title ASC";
          $stmt = mysqli_prepare($conn, $sql);
          mysqli_stmt_bind_param($stmt, "s", $cat_filter);
          mysqli_stmt_execute($stmt);
          $result = mysqli_stmt_get_result($stmt);
      } 
      else {
          // No category clicked - Show ALL books
          $sql = "SELECT * FROM books ORDER BY book_title ASC";
          $result = mysqli_query($conn, $sql);
      }

      // --- DISPLAY LOOP ---
      if(mysqli_num_rows($result) > 0) {
          while($book = mysqli_fetch_assoc($result)) {
              $canBorrow = $book['quantity'] > 0;
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
                    
                    <!-- Display stock with color indicator -->
                    <div class="book-stock <?php echo (!$canBorrow) ? 'out-of-stock' : ''; ?>">
                        Stock: <strong><?php echo $book['quantity']; ?></strong>
                        <?php if(!$canBorrow): ?>
                            <span class="stock-label">(Unavailable)</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Borrow Button -->
                    <?php if ($canBorrow): ?>
                        <button type="button" 
                                class="borrow-btn" 
                                onclick="showBorrowModal(
                                    '<?php echo htmlspecialchars($book['book_title']); ?>',
                                    '<?php echo htmlspecialchars($book['author']); ?>',
                                    '<?php echo htmlspecialchars($book['isbn']); ?>',
                                    '<?php echo htmlspecialchars($book['book_category']); ?>',
                                    <?php echo $book['quantity']; ?>,
                                    <?php echo $userLoggedIn ? 'true' : 'false'; ?>
                                )">
                            <?php echo $userLoggedIn ? 'Borrow Book' : 'Login to Borrow'; ?>
                        </button>
                    <?php else: ?>
                        <button type="button" 
                                class="borrow-btn" 
                                disabled 
                                title="This book is currently unavailable">
                            Out of Stock
                        </button>
                    <?php endif; ?>
                </section>

              </section>
              <?php
          }
      } else {
          // Message if category is empty
          echo "<p style='color:white; grid-column: 1/-1; text-align:center;'>No books found in this category.</p>";
      }
      
      // Clean up statement if it exists, otherwise free result
      if(isset($stmt)) { mysqli_stmt_close($stmt); }
      else { mysqli_free_result($result); }
      ?>
    </section>
  </section>

  <!-- Borrow Confirmation Modal -->
  <div id="borrowModal" class="borrow-modal-overlay">
    <div class="borrow-modal">
      <button class="modal-close" onclick="closeBorrowModal()">&times;</button>
      
      <div class="modal-header">
        <h2>📚 Borrow Book Confirmation</h2>
      </div>
      
      <div class="modal-body">
        <div class="book-info-row">
          <span class="book-info-label">Book Title:</span>
          <span class="book-info-value" id="modalBookTitle"></span>
        </div>
        
        <div class="book-info-row">
          <span class="book-info-label">Author:</span>
          <span class="book-info-value" id="modalAuthor"></span>
        </div>
        
        <div class="book-info-row">
          <span class="book-info-label">ISBN:</span>
          <span class="book-info-value" id="modalISBN"></span>
        </div>
        
        <div class="book-info-row">
          <span class="book-info-label">Category:</span>
          <span class="book-info-value" id="modalCategory"></span>
        </div>
        
        <div class="book-info-row">
          <span class="book-info-label">Available Stock:</span>
          <span class="book-info-value" id="modalStock"></span>
        </div>
        
        <div class="date-info">
          <div class="date-row">
            <span class="date-label">📅 Borrow Date:</span>
            <span class="date-value" id="borrowDate"></span>
          </div>
          
          <div class="date-row return-date-highlight">
            <span class="date-label">⏰ Return Date:</span>
            <span class="date-value" id="returnDate"></span>
          </div>
        </div>
      </div>
      
      <div class="modal-footer">
        <form method="post" id="confirmBorrowForm" style="display: inline;">
          <input type="hidden" name="book_isbn" id="hiddenISBN">
          <button type="submit" name="borrow_book" class="modal-btn btn-confirm-borrow">
            ✓ Confirm Borrow
          </button>
        </form>
        <button type="button" class="modal-btn btn-cancel-borrow" onclick="closeBorrowModal()">
          ✗ Cancel
        </button>
      </div>
    </div>
  </div>

  <script>
    function showBorrowModal(title, author, isbn, category, stock, isLoggedIn) {
      // Check if user is logged in
      if (!isLoggedIn) {
        alert('Please log in first to borrow books!');
        window.location.href = 'user-login.php';
        return;
      }
      
      // Populate modal with book information
      document.getElementById('modalBookTitle').textContent = title;
      document.getElementById('modalAuthor').textContent = author;
      document.getElementById('modalISBN').textContent = isbn;
      document.getElementById('modalCategory').textContent = category;
      document.getElementById('modalStock').textContent = stock;
      document.getElementById('hiddenISBN').value = isbn;
      
      // Calculate dates
      const today = new Date();
      const returnDate = new Date();
      returnDate.setDate(today.getDate() + 3); // 3 days from today
      
      // Format dates
      const formatDate = (date) => {
        const options = { 
          weekday: 'long', 
          year: 'numeric', 
          month: 'long', 
          day: 'numeric' 
        };
        return date.toLocaleDateString('en-US', options);
      };
      
      document.getElementById('borrowDate').textContent = formatDate(today);
      document.getElementById('returnDate').textContent = formatDate(returnDate);
      
      // Show modal
      document.getElementById('borrowModal').style.display = 'flex';
    }
    
    function closeBorrowModal() {
      document.getElementById('borrowModal').style.display = 'none';
    }
    
    // Close modal when clicking outside of it
    window.onclick = function(event) {
      const modal = document.getElementById('borrowModal');
      if (event.target === modal) {
        closeBorrowModal();
      }
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeBorrowModal();
      }
    });
  </script>

</body>

<section>
    <?php include('footer.php'); ?>
</section>

</html>