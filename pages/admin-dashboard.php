<?php 
  include('../config/database.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../styles/dashboard.css">
</head>
<body>
  <header>
    <section>
      <?php include 'header.php';?>
    </section>
  </header>
  <main>
    <section class="main-container">
        <aside class="side-nav">
        <section class="sidenav-link">
          <a>
            <img src="../images/icons/home-icon.svg">
          </a>
        </section>
        <section class="sidenav-link">
          <a>
            <img src="../images/icons/settings-icon.svg">
          </a>
        </section>
        <section class="sidenav-link">
          <a>
            <img src="../images/icons/report-icon.svg">
          </a>
        </section>
        <section class="sidenav-link">
          <a>
            <img src="../images/icons/info-icon.svg">
          </a>
        </section>
        <section class="sidenav-link">
          <a>
            <img src="../images/icons/account-icon.svg">
          </a>
        </section>
        </aside>
      <section class="information-cards-grid">
        <section>
          <section class="information-card">
            <h2>Total Visitor: </h2>

          </section>        
        </section>
        <section>Test</section>
        <section>Test</section>
      </section>
    </section>
  </main>
</body>
</html>