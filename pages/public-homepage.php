<?php
 include("../config/database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Homepage</title>
  <link rel="stylesheet" href="../styles/admin-homepage.css">
</head>
<body>
  
  <header><?php include('public-header.php');?></header>
  <section><?php include('public-nav.php');?></section>
  <section><?php include('book-category.php');?></section>

  <section class="book-catalog-container">
    
    <section class="book-catalog">
      <?php
      // --- LOGIC TO FILTER BOOKS ---
      
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
          // No category clicked Show ALL books
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
    </section>

    <form method="post" class="qty-control" 
          onsubmit="return <?php echo ($book['quantity'] == 0) ? 'confirmDelete(event)' : 'true'; ?>">
        <input type="hidden" name="book_isbn" value="<?php echo $book['isbn']; ?>">
        
        <?php if($book['quantity'] > 0): ?>
            <!-- Show +/- buttons when quantity > 0 -->
            <button type="submit" name="update_qty" value="minus" class="qty-btn minus">-</button>
            
            <span class="qty-display">
                Qty: <strong><?php echo $book['quantity']; ?></strong>
            </span>
            
            <button type="submit" name="update_qty" value="plus" class="qty-btn plus">+</button>
            
    <?php else: ?>
    <!-- Show delete AND restore buttons when quantity = 0 -->
    <span class="qty-display zero">
        Qty: <strong>0</strong>
    </span>
    
    <div class="zero-actions">
        <button type="submit" name="restore_qty" class="restore-btn" title="Restore to 1">
            ↻
        </button>
        <button type="submit" name="delete_book" class="delete-btn">
            🗑️ Delete
        </button>
    </div>
<?php endif; ?>
    </form>
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

  <script>
  function confirmDelete(event, form) {
      // Show confirmation dialog
      const confirmed = confirm(
          "⚠️ WARNING!\n\n" +
          "This book has 0 quantity.\n" +
          "Do you want to PERMANENTLY DELETE this book?\n\n" +
          "This action cannot be undone!\n\n" +
          "Click OK to DELETE\n" +
          "Click Cancel to RESTORE quantity to 1"
      );
      
      // If user clicks "Cancel", restore quantity to 1
      if (!confirmed) {
          event.preventDefault();
          
          // Trigger the restore button click
          const restoreBtn = form.querySelector('[name="restore_qty"]');
          if (restoreBtn) {
              restoreBtn.click();
          }
          
          return false;
      }
      
      // If user clicks "OK", allow delete to proceed
      return true;
  }
  </script>

</body>
<section><?php include('footer.php');?></section>
</html>
