<?php
// fetch/user-logout-fetch.php - Handles user logout

// Start session if it hasn't been started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the sign out button was clicked (POST method only)
if(isset($_POST['signout'])) {
    // Destroy all session variables (remove stored data like userId, userName)
    session_unset();
    // Completely destroy the session
    session_destroy();
    
    // Redirect to public homepage (guest view)
    header("Location: ../pages/public-homepage.php");
    exit();
} else {
    // If user accesses this file directly without posting, redirect to homepage anyway
    header("Location: ../pages/public-homepage.php");
    exit();
}
?>