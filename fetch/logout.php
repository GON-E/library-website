<?php
// logout.php
  include('../config/admin-auth.php');

// If the sign out button is clicked
if(isset($_POST['signout']) || isset($_GET['signout'])) {
    // Destroy all session data
    session_unset();
    session_destroy();
    
    // Redirect to login page
    header("Location: ../pages/admin-login.php");
    exit();
}
?>
