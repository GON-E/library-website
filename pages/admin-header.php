<?php
  include_once('../config/database.php');
  include('../config/admin-auth.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../styles/admin-header.css">
  <script src="../script/admin-header.js" defer></script>
</head>
<body>

  <header>
    <section class="upper-header">
      <section class="signout-container">
          <form action="../fetch/logout.php" method="post">
    <button type="submit" name="signout" class="signout-btn">Sign Out</button>
  </form>
      </section>


      <section >
        <form action="../actions/add-book.php" method="get">
        <button type="submit" name="modify" class="signout-btn">Modify Books</button>
  </form>
      </section>


    </section>
    <section class="lower-header">
        <section class="lower-header-content">
        <section>
          <h1>Hello, <?php echo  htmlspecialchars($_SESSION['admin_name'] ?? 'Admin')?>
      
          Welcome to Lé Bros Library!</h1  >
        </section>


        <section class="time-date"> 
<?php date_default_timezone_set('Asia/Manila'); ?>

<div id="liveClock"></div>

<script>
const initialTime = "<?php echo date('Y-m-d H:i:s'); ?>";
</script>
    </section>
  </header>
</body>
</html>