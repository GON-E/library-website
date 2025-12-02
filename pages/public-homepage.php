<?php
// pages/public-homepage.php - PUBLIC VIEW (No login required)
// Guests browse books here, must login to borrow

if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('../config/database.php');

// Check if user is logged in
$isLoggedIn = isset($_SESSION['userId']) && !empty($_SESSION['userId']);

// If user IS logged in, redirect to user-homepage (browse books as logged in user)
if($isLoggedIn) {
    header("Location: user-homepage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Browse Books - Lé Bros Library</title>
  <link rel="stylesheet" href="../styles/public-homepage.css">
</head>
<body>

  <header>
    <?php include('public-header.php'); ?>
  </header>

  <section>
    <?php include('book-category.php'); ?>
  </section>

  <!-- Login Required Modal -->
  <div id="loginModal" class="login-required-overlay">
    <div class="login-prompt">
      <h2>🔒 Login Required</h2>
      <p>You need to be logged in to borrow books from our library.</p>
      <div class="login-prompt-buttons">
        <a href="user-login.php" class="btn-login-redirect">Login</a>
        <a href="user-signup.php" class="btn-signup-redirect">Sign Up</a>
        <button onclick="closeLoginModal()" class="btn-cancel">Cancel</button>
      </div>
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
                            onclick="showLoginModal()">
                        <?php echo ($book['quantity'] > 0) ? '🔒 Login to Borrow' : 'Out of Stock'; ?>
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

  <script src="../script/public-homepage.js"></script>

</body>

<section>
    <?php include('footer.php'); ?>
</section>

</html>