<?php
// pages/user-dashboard.php - MY BORROWED BOOKS (Dashboard View)
session_start();
include('../config/database.php');

// Check if user is logged in
if(!isset($_SESSION['userId'])) {
    header("Location: ../pages/user-login.php");
    exit();
}

$user_id = $_SESSION['userId'];

// Get borrowed books statistics
$sql_stats = "SELECT 
    COUNT(CASE WHEN status = 'borrowed' THEN 1 END) as active_borrows,
    COUNT(CASE WHEN status = 'borrowed' AND due_date < CURDATE() THEN 1 END) as overdue_count,
    COUNT(*) as total_borrowed
    FROM borrow_records 
    WHERE user_id = ?";

$stmt_stats = mysqli_prepare($conn, $sql_stats);
mysqli_stmt_bind_param($stmt_stats, "i", $user_id);
mysqli_stmt_execute($stmt_stats);
$stats_result = mysqli_stmt_get_result($stmt_stats);
$stats = mysqli_fetch_assoc($stats_result);
mysqli_stmt_close($stmt_stats);

// Get borrowed books - join with books table
$sql = "SELECT br.*, b.book_title, b.author, b.image, b.isbn 
        FROM borrow_records br
        JOIN books b ON br.book_id = b.bookId
        WHERE br.user_id = ?
        ORDER BY br.date_borrowed DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Dashboard - Lé Bros Library</title>
  <link rel="stylesheet" href="../styles/user-dashboard.css">
</head>
<body>

  <header><?php include('user-header.php'); ?></header>
  <section><?php include('user-nav.php'); ?></section>

  <main>
    <div class="main-container">
      
      <!-- Statistics Cards -->
      <section class="information-cards-grid">
        <section class="information-card">
          <section class="card-title">
            <h2>Currently Borrowed</h2>
            <img src="../images/icons/borrow-icon.svg">
          </section>
          <section class="card-count">
            <h1><?php echo $stats['active_borrows']; ?></h1>
          </section>
        </section>

        <section class="information-card">
          <section class="card-title">
            <h2>Overdue Books</h2>
            <img src="../images/icons/timer-icon.svg">
          </section>
          <section class="card-count">
            <h1 style="<?php echo $stats['overdue_count'] > 0 ? 'color: #ff4444;' : ''; ?>">
              <?php echo $stats['overdue_count']; ?>
            </h1>
          </section>
        </section>

        <section class="information-card">
          <section class="card-title">
            <h2>Total Borrowed</h2>
            <img src="../images/icons/visitor-icon.svg">
          </section>
          <section class="card-count">
            <h1><?php echo $stats['total_borrowed']; ?></h1>
          </section>
        </section>
      </section>

      <!-- Recently Borrowed Books Section -->
      <section class="information-table-container">
        <h2>My Borrowed Books</h2>
        
        <?php if(mysqli_num_rows($result) > 0): ?>
          <table class="table-wrap">
            <thead>
              <tr>
                <th>Book Title</th>
                <th>Author</th>
                <th>Date Borrowed</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Days Remaining</th>
              </tr>
            </thead>
            <tbody>
              <?php while($book = mysqli_fetch_assoc($result)): ?>
                <?php
                  $status = $book['status'];
                  $today = date('Y-m-d');
                  $days_left = ceil((strtotime($book['due_date']) - time()) / 86400);
                  
                  if($status == 'borrowed' && $book['due_date'] < $today) {
                    $status = 'overdue';
                  }
                  
                  $status_class = $status == 'overdue' ? 'style="color: #ff4444; font-weight: bold;"' : 
                                 ($status == 'returned' ? 'style="color: #4CAF50;"' : '');
                ?>
                <tr>
                  <td><?php echo htmlspecialchars($book['book_title']); ?></td>
                  <td><?php echo htmlspecialchars($book['author']); ?></td>
                  <td><?php echo date('M j, Y', strtotime($book['date_borrowed'])); ?></td>
                  <td><?php echo date('M j, Y', strtotime($book['due_date'])); ?></td>
                  <td <?php echo $status_class; ?>><?php echo strtoupper($status); ?></td>
                  <td>
                    <?php 
                      if($status == 'returned') {
                        echo '-';
                      } elseif($days_left > 0) {
                        echo "<span style='color: #4CAF50;'>$days_left days</span>";
                      } else {
                        echo "<span style='color: #ff4444;'>" . abs($days_left) . " days late</span>";
                      }
                    ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p style="text-align: center; padding: 50px; color: white;">
            You haven't borrowed any books yet.<br><br>
            <a href="user-homepage.php" style="color: #4CAF50; text-decoration: none; font-weight: bold;">
              📚 Browse Available Books
            </a>
          </p>
        <?php endif; ?>
      </section>

    </div>
  </main>

</body>
<section><?php include('footer.php'); ?></section>
</html>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>