<?php
include('../config/database.php');
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
    <?php include('public-header.php'); ?>
  </header>

  <section>
    <?php include('public-nav.php'); ?>
  </section>

  <section>
    <?php include('book-category.php'); ?>
  </section>

  <section class="book-catalog-container">
    
    <section class="book-catalog">
      <?php
      // --- LOGIC TO FILTER BOOKS (Same as admin-homepage) ---
      
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
          // 2. No category clicked  Show ALL books
          $sql = "SELECT * FROM books ORDER BY book_title ASC";
          $result = mysqli_query($conn, $sql);
      }

      // --- DISPLAY LOOP ---
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
                    
                    <!-- Display stock -->
                    <div class="book-stock">
                        Stock: <strong><?php echo $book['quantity']; ?></strong>
                    </div>
                    
                  <!-- Borrow Button-->
                    <form method="post" class="borrow-form">
                        <input type="hidden" name="book_isbn" value="<?php echo $book['isbn']; ?>">
                        <button type="submit" name="borrow_book" class="borrow-btn">
                            Borrow Book
                        </button>
                    </form>
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

</body>

<section>
    <?php include('footer.php'); ?>
</section>

</html>