<?php
  include('../config/database.php');
  include('../fetch/add-delete-fetch.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Books</title>
  <link rel="stylesheet" href="../styles/add-book.css">
  <link rel="icon" href="../images/favicon.jpg" type="image/x-icon">
</head>
<body>

  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post" enctype="multipart/form-data">
    <section>
      <h2>Add Book</h2>
      
      

      Book Title:
      <input type="text" name="title" required> <br>
      
      Author:
      <input type="text" name="author" required><br>
      
      Year Published: 
      <input type="date" name="publish" required> <br>
    
      Book Type:
    <select name="bookType" required> 
    <option value="" disabled selected>Select a category...</option>
    <option value="Entertainment">Entertainment</option>
    <option value="Science">Science</option>
    <option value="History">History</option>
    <option value="Mathematics">Mathematics</option>
    <option value="Electronics">Electronics</option>
    <option value="Novel">Novel</option>
    <option value="Cooking">Cooking</option>
  </select>

      ISBN:
      <input type="text" name="isbn" required> <br>
      
      Quantity:
      <input type="number" name="quantity" required> <br>
      
      Image:
      <input type="file" name="bookImage" accept="image/*"> <br>
      
      <input type="submit" name="register" value="Register">

      <a href="../pages/admin-homepage.php" class="home-btn"> Admin homepage </a> <br>
    </section>
  </form> 
  <hr>  


  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
    <section>
      <h2 id="delete">Delete Book</h2>
      
      Enter ISBN to Delete:
      <input type="text" name="isbn" placeholder="Type ISBN here..." required>
      
      <input type="submit" name="delete_btn" value="Delete">
    </section>
  </form>

</body>
</html>