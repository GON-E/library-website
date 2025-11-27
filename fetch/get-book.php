<?php 
  include('../config/database.php');

  $sql = "SELECT * FROM books";

  $result = $conn->query($sql);

  $books = [];

  if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()){
      $books = $row;
    }
  } 

?>  