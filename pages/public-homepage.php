<?php
 include("../config/database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../styles/public-homepage.css">
</head>
<body>
 
<section><?php include('side-nav.php');?></section>
  
    <!-- <button img src="" name="logo" href="#"></button> -->
    <div class="main_container">
        <div class="welcome">
            <header>
              <h1>Hello, User! Welcome to Le' Bros Library!</h1> <!-- "user" name of the user-->
            <div class="date">November 00, 2025 | Monday, 00:00 AM</div>

            <div class="header-buttons">
              <button class="login_btn">Log-In</button>
               <button class="signup_btn">Sign-up</button>
            </div>
             </header>

            <div class="Category">
                <a href="#">Technology |</a>
                <a href="#">Entertainment |</a>
                <a href="#">Science |</a>
                <a href="#">History |</a>
                <a href="#">Mathematic |</a>
                <a href="#">Electonics |</a>
                <a href="#">Nature |</a>
                <a href="#">Cooking |</a>
            </div>
        </div>
            
        <div>

        </div>
    </div>
   
</body>

<section><?php include('footer.php');?></section>
</html>
