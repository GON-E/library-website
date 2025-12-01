<?php
  include('../config/database.php');
  
  // Only check admin auth if we're on an admin page
  // Check if the current page is an admin page
  $current_page = basename($_SERVER['PHP_SELF']);
  if (strpos($current_page, 'admin-') !== false) {
    include('../config/admin-auth.php');
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Catalog</title>
  <link rel="stylesheet" href="../styles/book-category.css">
</head>
<body>
  <?php 
    $current_cat = isset($_GET['category']) ? $_GET['category'] : '';
    
    // Determine which page to link to based on current page
    $base_page = (strpos($current_page, 'admin-') !== false) ? 'admin-homepage.php' : 'public-homepage.php';
  ?>
  <main class="book-category">

    <a href="<?php echo $base_page; ?>"
      class="category-link <?php echo ($current_cat == '') ? 'active': ''; ?>">All Books 
    </a>

    <a href="<?php echo $base_page; ?>?category=Entertainment" 
       class="category-link <?php echo ($current_cat == 'Entertainment') ? 'active' : ''; ?>">
       Entertainment
    </a>

    <a href="<?php echo $base_page; ?>?category=Science" 
       class="category-link <?php echo ($current_cat == 'Science') ? 'active' : ''; ?>">
       Science
    </a>

    <a href="<?php echo $base_page; ?>?category=History" 
       class="category-link <?php echo ($current_cat == 'History') ? 'active' : ''; ?>">
       History
    </a>

    <a href="<?php echo $base_page; ?>?category=Mathematics" 
       class="category-link <?php echo ($current_cat == 'Mathematics') ? 'active' : ''; ?>">
       Mathematics
    </a>

    <a href="<?php echo $base_page; ?>?category=Electronics" 
       class="category-link <?php echo ($current_cat == 'Electronics') ? 'active' : ''; ?>">
       Electronics
    </a>

    <a href="<?php echo $base_page; ?>?category=Novel" 
       class="category-link <?php echo ($current_cat == 'Novel') ? 'active' : ''; ?>">
       Novel
    </a>

    <a href="<?php echo $base_page; ?>?category=Cooking" 
       class="category-link <?php echo ($current_cat == 'Cooking') ? 'active' : ''; ?>">
       Cooking
    </a>

    
  </main>

</body>
</html>