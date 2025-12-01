<?php
  include('../config/database.php')
?>
   
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styles/book-category.css">
</head>
<body>
  <?php 
    $current_cat = isset($_GET['category']) ? $_GET['category'] : '';
    
  ?>
  <main class="book-category">

    <a href="admin-homepage.php"
      class="category-link <?php echo ($current_cat == '') ? 'active': ''; ?>">All Books 
    </a>

    <a href="admin-homepage.php?category=Entertainment" 
       class="category-link <?php echo ($current_cat == 'Entertainment') ? 'active' : ''; ?>">
       Entertainment
    </a>

    <a href="admin-homepage.php?category=Science" 
       class="category-link <?php echo ($current_cat == 'Science') ? 'active' : ''; ?>">
       Science
    </a>

    <a href="admin-homepage.php?category=History" 
       class="category-link <?php echo ($current_cat == 'History') ? 'active' : ''; ?>">
       History
    </a>

    <a href="admin-homepage.php?category=Mathematics" 
       class="category-link <?php echo ($current_cat == 'Mathematics') ? 'active' : ''; ?>">
       Mathematics
    </a>

    <a href="admin-homepage.php?category=Electronics" 
       class="category-link <?php echo ($current_cat == 'Electronics') ? 'active' : ''; ?>">
       Electronics
    </a>

    <a href="admin-homepage.php?category=Novel" 
       class="category-link <?php echo ($current_cat == 'Novel') ? 'active' : ''; ?>">
       Novel
    </a>

    <a href="admin-homepage.php?category=Cooking" 
       class="category-link <?php echo ($current_cat == 'Cooking') ? 'active' : ''; ?>">
       Cooking
    </a>

    
  </main>

</body>
</html>