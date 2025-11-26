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
        <section class="information-card">
          <section class="card-title">
            <h2>Total Visitor: </h2>
            <img src="../images/icons/visitor-icon.svg">
          </section>
          <section class="card-count">
            <h1>00</h1>
          </section>     
        </section>

        <section class="information-card">
          <section class="card-title">
            <h2>Borrowed Books:  </h2>
            <img src="../images/icons/borrow-icon.svg">
          </section>
          <section class="card-count">
            <h1>00</h1>
          </section>     
        </section>

        <section class="information-card">
          <section class="card-title">
            <h2>Unreturned Rooks: </h2>
            <img src="../images/icons/timer-icon.svg">
          </section>
          <section class="card-count">
            <h1>00</h1>
          </section>     
      </section>
    </section>

    <section class="information-table-container">
      <h2>Recently Borrowed Books:</h2>
      <table class="table-wrap">
          <thead>
            <tr>
                <th>Borrower Name</th>
                <th>Book Name</th>
                <th>BookId</th>
                <th>BookType</th>
                <th>Date Borrowed</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Row 1, Cell 1</td>
                <td>Row 1, Cell 2</td>
                <td>Row 1, Cell 3</td>
                <td>Row 1, Cell 4</td>
                <td>Row 1, Cell 5</td>
            </tr>
            <tr>

            </tr>
            <tr>
            </tr>
        </tbody>
      </table>
    </section>
  </main>
</body>
</html>