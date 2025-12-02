<?php
  // Include the database connection file so other includes can use $conn if needed.
  include('../config/database.php');

  // Get the current filename (for example: 'public-homepage.php').
  // We use this to decide whether we are on an admin, user, or public page.
  $current_page = basename($_SERVER['PHP_SELF']);

  // If the current page looks like an admin page, include admin auth to protect it.
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
  <!-- Load styles for the category bar (keeps the same look/design) -->
  <link rel="stylesheet" href="../styles/book-category.css">
</head>
<body>
  <?php 
    // Read the selected category from the URL query string, e.g. ?category=Science
    // If not present we use an empty string so 'All Books' becomes active.
    $current_cat = isset($_GET['category']) ? $_GET['category'] : '';
    
    // Decide which homepage file the links should point to. This keeps users
    // on the same area (admin, user, or public) when they click categories.
    if (strpos($current_page, 'admin-') !== false) {
      // We are on an admin page, link back to admin homepage
      $base_page = 'admin-homepage.php';
    } elseif (strpos($current_page, 'user-') !== false) {
      // We are on a user page, link back to user homepage
      $base_page = 'user-homepage.php';
    } else {
      // Default for guests: public homepage
      $base_page = 'public-homepage.php';
    }
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