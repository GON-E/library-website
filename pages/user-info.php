<?php
 include("../config/database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <link rel="stylesheet" href="../styles/user-info.css">
   <link rel="icon" href="../images/icons/bookIcon.png" type="image/png">
   
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Info</title>
</head>

<body>
   <?php include('user-nav.php');?>

   <main class="main-container">

      <section class="about">
         <h1>ABOUT OUR SITE</h1>
         <p>
            Welcome to Lé Bros Library, a modern digital library management system designed to make 
            book borrowing simple, efficient, and accessible
             for everyone. Our platform bridges the gap between traditional library services and 
             contemporary technology, offering users the ability to browse our extensive collection of
             books across multiple categories including Entertainment, Science, History, Mathematics, 
             Electronics, Novels, and Cooking. Whether you're a student seeking academic resources, a 
             professional looking for technical references, or simply an avid reader exploring new stories, 
             our library provides a user-friendly interface that makes discovering and borrowing books 
             easier than ever before.
         </p>
         <p>
            Our system empowers both library administrators and users with powerful
            tools for managing book inventories and tracking borrowed materials. 
            Administrators can efficiently add new books, update quantities, and monitor 
            borrowing activity through an intuitive dashboard, while registered users enjoy 
            the convenience of browsing available titles, borrowing books with a simple click, 
            and managing their borrowed items through a personalized dashboard. With features 
            like a 7-day borrowing period, real-time inventory tracking, and overdue notifications, 
            we ensure a transparent and organized lending process. Built as an educational project, 
            Lé Bros Library demonstrates the potential of web-based solutions in modernizing library
             operations while maintaining the warmth and accessibility that make libraries 
             essential community resources.
         </p>
      </section>

      <section class="contact">
         <h3>CONTACT US</h3>

         <div class="info-item">
            <strong>Contact No:</strong> 
            <a href="tel:+639218931232">+639218931232</a>
         </div>

         <div class="info-item">
            <strong>Email:</strong> 
            <a href="mailto:Lebros67@gmail.com">Lebros67@gmail.com</a>
         </div>

         <div class="info-item">
            <strong>Location:</strong> 
            2543 Jose Burgos St, Calamba, 4027 Laguna
         </div>
      </section>

   </main>

   <?php include('footer.php');?>
</body>
</html>
