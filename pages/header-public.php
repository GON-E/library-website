<?php
// Ensure session is started to access the 'admin_name' variable
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
hi
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../styles/header.css">
  <script src="../script/header.js" defer></script>
</head>
<body>
  <header>
    <section class="upper-header">
      <section class="signout-container">
        <button class="signout-btn">Sign Out</button>
      </section>
    </section>
    <section class="lower-header">
        <section class="lower-header-content">
        <section>
          <h1>Hello, US! Welcome to Lé Bros Library!</h1  >
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