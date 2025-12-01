<?php 
// config/user-auth.php

// Start the Session if not already started
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is NOT logged in
if(!isset($_SESSION['userId']) || empty($_SESSION['userId'])) {
    // If not logged in, redirect to login page
    header("Location: ../pages/user-login.php");
    exit();
}
?>