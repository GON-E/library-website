<?php  
  $db_server = "localhost"; // The Database Server
  $db_user = "root"; // default MySQL username
  $db_pass = ""; // default MySQL password
  $db_name = "librarysysdb"; // Name of the Database  

  // For Database Connectivity
  $conn = "";

  try { // Try to connect
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
    echo "Database Connected! <br>";
  } catch(mysqli_sql_exception) { // Catch if a fatal error occurs
    echo "Could not Connect! <br>";
  }
?>