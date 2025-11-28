<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../styles/header.css">
</head>
<kdam>
  <header>
    <section class="upper-header">

    </section>
    <section class="lower-header">
      <!--
        <section class="blank-green-bg">
          
        </section>  
-->
        <section class="lower-header-content">
        <section>
          <h1>Hello, Admin! Welcome to Lé Bros Library!</h1  >
        </section>
        <section class="time-date"> 
<?php date_default_timezone_set('Asia/Manila'); ?>

<div id="liveClock"></div>

<script>
const initialTime = "<?php echo date('Y-m-d H:i:s'); ?>";
</script>

<script src="assets/script.js"></script>

    <!--
    -->
    </section>
  </header>
   <script src="../script/header.js"> </script>
</body>
</html>