<?php
session_start();
include('../config/database.php');
include('../fetch/borrow-book-fetch.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Public Homepage</title>
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

  <!-- Display success/error messages -->
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
                    
                    <div class="book-stock">
                        Stock: <strong><?php echo $book['quantity']; ?></strong>
                    </div>
                    
                    <form method="post" class="borrow-form">
                        <input type="hidden" name="book_isbn" value="<?php echo $book['isbn']; ?>">
                        <button type="submit" name="borrow_book" class="borrow-btn" 
                                <?php echo ($book['quantity'] <= 0) ? 'disabled' : ''; ?>>
                            <?php echo ($book['quantity'] > 0) ? 'Borrow Book' : 'Out of Stock'; ?>
                        </button>
                    </form>
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

</body>

<section>
    <?php include('footer.php'); ?>
</section>

</html>