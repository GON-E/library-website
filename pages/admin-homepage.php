<?php
include('../config/database.php');

// --- HANDLE QUANTITY UPDATE ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_qty'])) {
    $isbn_to_update = $_POST['book_isbn'];
    $action = $_POST['update_qty'];

    if ($action == 'plus') {
        $update_sql = "UPDATE books SET quantity = quantity + 1 WHERE isbn = ?";
    } elseif ($action == 'minus') {
        // GREATEST(0, ...) prevents the number from going below 0
        $update_sql = "UPDATE books SET quantity = GREATEST(0, quantity - 1) WHERE isbn = ?";
    }

    if(isset($update_sql)) {
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "s", $isbn_to_update);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // Refresh page to show new number and prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
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
  
  <header><?php include('header.php');?></header>
  <section><?php include('side-nav.php');?></section>
  <section><?php include('book-category.php');?></section>

  <section class="book-catalog-container">
    
    <section class="book-catalog">
      <?php
      $sql = "SELECT * FROM books ORDER BY book_title ASC";
      $result = mysqli_query($conn, $sql);

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

                <form method="post" class="qty-control">
                    <input type="hidden" name="book_isbn" value="<?php echo $book['isbn']; ?>">
                    
                    <button type="submit" name="update_qty" value="minus" class="qty-btn minus">-</button>
                    
                    <span class="qty-display">
                        Qty: <strong><?php echo $book['quantity']; ?></strong>
                    </span>
                    
                    <button type="submit" name="update_qty" value="plus" class="qty-btn plus">+</button>
                </form>

              </section>
              <?php
          }
      } else {
          echo "<p style='color:white;'>No books available.</p>";
      }
      mysqli_free_result($result);
      ?>
    </section>
  </section>

</body>
</html>