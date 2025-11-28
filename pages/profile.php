<?php 
  include('../config/database.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Profile Page</title>
  <link rel="stylesheet" href="../styles/profile.css" />
</head>
<body>

  <main class="profile-container" role="main" aria-labelledby="profile-heading">
   
    <img src="your-photo.jpg" alt="Profile picture of Your Name" />

    <h2 id="profile-heading">Your Name</h2>
    <p class="email">your.email@example.com</p>

    <div class="profile-info" aria-label="Profile details">
      <p><strong>Username:</strong> username123</p>
      <p><strong>Role:</strong> Admin / User</p>
      <p><strong>Location:</strong> Your City, Country</p>
    </div>

    <div class="actions">
      <a href="admin-dashboard.php" class="btn" role="button">Back Home</a>
    </div>
  </main>

</body>
<section><?php include('footer.php');?></section>
</html>



