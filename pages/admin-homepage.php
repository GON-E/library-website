<?php
  include('../config/database.php');
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
  <header>
    <?php include('header.php');?>  
  </header>
  <section>
    <?php include('side-nav.php');?>
  </section>
  <section>
    <?php include('book-category.php');?>
  </section>

  <section class="book-catalog-container">
    
    <section class="book-catalog">
      <?php
      $sql = "SELECT * FROM books ORDER BY book_title ASC";
      $result = mysqli_query($conn, $sql);

      if(mysqli_num_rows($result) > 0) {
          // Loop generates CARDS, not entire grids
          while($book = mysqli_fetch_assoc($result)) {
              ?>
              <section class="book-info-container">
                
                <section class="book-title">
                  <?php echo htmlspecialchars($book['book_title']); ?>
                </section>
                
                <section class="image-container">
                   <?php if(!empty($book['image'])): ?>
                    <img src="../images/books/<?php echo htmlspecialchars($book['image']); ?>" 
                         alt="<?php echo htmlspecialchars($book['book_title']); ?>">
                  <?php else: ?>
                    <div class="no-image">No Image</div>
                  <?php endif; ?>
                </section>
                
                <section class="author-name-footer">
                  <?php echo htmlspecialchars($book['author']); ?>
                </section>

              </section>
              <?php
          }
      } else {
          echo "<p style='color:white;'>No books available.</p>";
      }
      mysqli_free_result($result);
      ?>
    </section>

    <section class="modify-button-container">
    </section>

  </section>

</body>
</html>

<?php
mysqli_close($conn);
?>