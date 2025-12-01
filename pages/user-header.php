<?php
// Ensure session is started to access user info
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get user name from session
$userName = isset($_SESSION['userName']) ? $_SESSION['userName'] : 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../styles/user-header.css">
  <script src="../script/header.js" defer></script>
</head>
<body>
  <header>
    <section class="upper-header">
      <section class="signout-container">
        <!-- User Sign Out button - FIXED PATH -->
        <form action="../fetch/user-logout.php" method="post" style="display: inline;">
          <button type="submit" name="signout" class="signout-btn">Sign Out</button>
        </form>
      </section>
      <section>
        <!-- Browse Books Button -->
        <a href="public-homepage.php">
          <button class="signout-btn">Browse Books</button>
        </a>
      </section>
    </section>
    <section class="lower-header">
        <section class="lower-header-content">
        <section>
          <h1>Hello, <?php echo htmlspecialchars($userName); ?>! Welcome to Lé Bros Library!</h1>
        </section>
        <section class="time-date"> 
<?php date_default_timezone_set('Asia/Manila'); ?>

<div id="liveClock"></div>

<script>
const initialTime = "<?php echo date('Y-m-d H:i:s'); ?>";
</script>
        </section>
      </section>
    </section>
  </header>
</body>
</html>