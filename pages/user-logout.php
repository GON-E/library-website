<?php
// fetch/user-logout.php

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If the sign out button is clicked
if(isset($_POST['signout'])) {
    // Destroy all session data
    session_unset();
    session_destroy();
    
    // Redirect to public homepage
    header("Location: ../pages/public-homepage.php");
    exit();
} else {
    // If accessed directly without POST, redirect to homepage
    header("Location: ../pages/public-homepage.php");
    exit();
}
?>