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
    <?php include('public-header.php'); ?>
      <!-- public-header.php removed -->
  </header>

  <section>
    <?php include('public-nav.php'); ?>
  </section>

  <section>
      <!-- book-category.php removed -->
    <?php include('book-category.php'); ?>
  </section>

  <section class="book-catalog-container">

    <section class="book-catalog">

      <!-- Example of a single book (STATIC SAMPLE) -->
      <section class="book-info-container">

        <div class="category-badge">
            Sample Category
        </div>

        <section class="image-container">
            <img src="../images/books/sample.jpg" alt="Sample Book">
        </section>

        <section class="book-details">
            <div class="book-title">Sample Book Title</div>
            <div class="author-name">Sample Author</div>
        </section>

      </section>

      <!-- Duplicate this block manually if needed -->
      <p style='color:white; grid-column: 1/-1; text-align:center;'>
        No books found in this category.
      </p>

    </section>
  </section>

</body>

<section>
    <!-- footer.php removed -->
</section>

</html>

