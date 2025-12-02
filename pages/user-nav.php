<?php
// Protect this navigation - only for logged-in users
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If user is not logged in, redirect to login
if(!isset($_SESSION['userId']) || empty($_SESSION['userId'])) {
    header("Location: ../pages/user-login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>User Dashboard</title> 
    
   <link rel="icon" href="../images/icons/bookIcon.png" type="image/png">
    
    <link rel="stylesheet" href="../styles/side-nav.css">
</head>
<body>
    <aside class="side-nav">

        <section class="sidenav-link">
            <a href="user-homepage.php" title="Browse Books">
                <img src="../images/icons/home-icon.svg">
            </a>
        </section>

        <section class="sidenav-link">
            <a href="user-dashboard.php" title="My Borrowed Books">
                <img src="../images/dashboard-icon.svg">
            </a>
        </section>

        <section class="sidenav-link">
            <a href="report-button.php" title="Report Issue">
                <img src="../images/icons/report-flag.png">
            </a>
        </section>

        <section class="sidenav-link">
            <a href="user-info.php" title="About">
                <img src="../images/icons/info-icon.svg">
            </a>
        </section>



    </aside>
</body>
</html>