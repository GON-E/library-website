<?php
// Ensure session is started to access user info
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['userId']) && !empty($_SESSION['userId']);
$userName = $isLoggedIn ? $_SESSION['userName'] : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../styles/public-header.css">
  <script src="../script/public-header.js" defer></script>
</head>
<body>
  <header>
    <section class="upper-header">
      <section class="signin-container">
        <?php if($isLoggedIn): ?>
          <!-- User is logged in - show Sign Out button -->
          <form action="../fetch/user-logout.php" method="post" style="display: inline;">
            <button type="submit" name="signout" class="signout-btn">Sign Out</button>
          </form>
        <?php else: ?>
          <!-- User is NOT logged in - show Login/Sign Up buttons -->
          <a href="user-login.php">
            <button class="signin-btn">Login</button>
          </a>
          <a href="user-signup.php">
            <button class="signin-btn">Sign Up</button>
          </a>
        <?php endif; ?>
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