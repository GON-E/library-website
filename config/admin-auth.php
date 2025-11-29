<?php 

  // Start the Session
  if(session_status() == PHP_SESSION_NONE) {
    session_start();
  }

  // Check if user is NOT logged in
  if(!isset($_SESSION['is_admin_logged_in']) || $_SESSION['is_admin_logged_in'] !== true) {
      // If not logged in, redirect to login page
      header("Location: ../pages/admin-login.php");
      exit();
  }
?>

