<?php
// fetch/logout.php - Handles admin logout

// Load and check admin authentication
include('../config/admin-auth.php');

// Check if the sign out button was clicked (via POST or GET)
if(isset($_POST['signout']) || isset($_GET['signout'])) {
    // Destroy all session variables (remove stored data like userId, username)
    session_unset();
    // Completely destroy the session
    session_destroy();
    
    // Redirect to the admin login page
    header("Location: ../pages/admin-login.php");
    exit();
}
?>
