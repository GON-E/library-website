<?php 
  include('../config/database.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="../styles/userreport.css">
  <link rel="icon" href="../images/icons/bookIcon.png" type="image/png">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>REPORT</title>
</head>

<body>
  <div class="main-container">
      <?php include('user-nav.php');?>

     <div class="container">
    <h2>Report an Issue</h2>

    <p>Select a problem:</p>

    <div class="option">
      <input type="radio" name="issue" id="bug">
      <label for="bug">Bug in the system</label>
    </div>

    <div class="option">
      <input type="radio" name="issue" id="buttons">
      <label for="buttons">Buttons not working</label>
    </div>

    <div class="option">
      <input type="radio" name="issue" id="other">
      <label for="other">Other problem</label>
      <label>(Describe the issue)</label>
    </div>

    <div class="textbox-area">
      <label></label>
      <textarea placeholder="Type the problem here..."></textarea>
    </div>
  
    
    <button class="submit-btn">Submit Report</button>

      <a class="back-btn" href="../pages/user-homepage.php">Home</a>
  </div>
  </div>
  <section class="footer-inc"><?php include('footer.php');?></section>
</body>
</html>