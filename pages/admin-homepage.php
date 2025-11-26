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

  <section class="book-catalog-container  ">
    <section class="book-catalog">
      <section class="book-info-container">
        <section class="book-title">Ang Sarap ni je</section>
        <section class="image-container">

        </section>
        <section class="author-name-footer">
          SADNADIJNDIQNDQNDIJQNDIQN
        </section>
      </section>
    </section>

    <section class="modify-button-container">
    </section>
  </section>
</body>
</html>