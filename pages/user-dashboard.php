<?php/* 
session_start();
include('../config/database.php');

// Check if user is logged in
if(!isset($_SESSION['userId'])) {
    header("Location: ../pages/user-login.php");
    exit();
}

$user_id = $_SESSION['userId'];

// Get user statistics
$sql_stats = "SELECT 
    COUNT(CASE WHEN status = 'borrowed' THEN 1 END) as currently_borrowed,
    COUNT(CASE WHEN status = 'borrowed' AND due_date < CURDATE() THEN 1 END) as overdue,
    COUNT(CASE WHEN status = 'returned' THEN 1 END) as returned_books
    FROM borrow_records 
    WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql_stats);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$stats = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Get recent activity
$sql_recent = "SELECT br.*, b.book_title, b.author 
               FROM borrow_records br
               JOIN books b ON br.book_id = b.bookId
               WHERE br.user_id = ?
               ORDER BY br.date_borrowed DESC
               LIMIT 10";

$stmt_recent = mysqli_prepare($conn, $sql_recent);
mysqli_stmt_bind_param($stmt_recent, "i", $user_id);
mysqli_stmt_execute($stmt_recent);
$recent_result = mysqli_stmt_get_result($stmt_recent);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Dashboard</title>
  <link rel="stylesheet" href="../styles/admin-dashboard.css">
</head>
<body>
  <header>
    <section>
      <?php include('user-header.php');?>
    </section>
  </header>
  
  <main>
    <section class="main-container"> 

      <?php include('user-nav.php');?>

      <section class="information-cards-grid">
        <section class="information-card">
          <section class="card-title">
            <h2>Currently Borrowed</h2>
            <img src="../images/icons/borrow-icon.svg">
          </section>
          <section class="card-count">
            <h1><?php echo $stats['currently_borrowed']; ?></h1>
          </section>     
        </section>

        <section class="information-card">
          <section class="card-title">
            <h2>Overdue Books</h2>
            <img src="../images/icons/timer-icon.svg">
          </section>
          <section class="card-count">
            <h1 style="<?php echo $stats['overdue'] > 0 ? 'color: #ff4444;' : ''; ?>">
              <?php echo $stats['overdue']; ?>
            </h1>
          </section>     
        </section>

        <section class="information-card">
          <section class="card-title">
            <h2>Returned Books</h2>
            <img src="../images/icons/visitor-icon.svg">
          </section>
          <section class="card-count">
            <h1><?php echo $stats['returned_books']; ?></h1>
          </section>     
      </section>
    </section>

    <section class="information-table-container">
      <h2>Recent Borrowing Activity</h2>
      
      <?php if(mysqli_num_rows($recent_result) > 0): ?>
        <table class="table-wrap">
          <thead>
            <tr>
                <th>Book Title</th>
                <th>Author</th>
                <th>Date Borrowed</th>
                <th>Due Date</th>
                <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($recent_result)): ?>
              <?php
                $status = $row['status'];
                if($status == 'borrowed' && $row['due_date'] < date('Y-m-d')) {
                  $status = 'overdue';
                }
                $status_color = $status == 'overdue' ? '#ff4444' : 
                               ($status == 'returned' ? '#4CAF50' : '#FFC107');
              ?>
              <tr>
                  <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                  <td><?php echo htmlspecialchars($row['author']); ?></td>
                  <td><?php echo date('M j, Y', strtotime($row['date_borrowed'])); ?></td>
                  <td><?php echo date('M j, Y', strtotime($row['due_date'])); ?></td>
                  <td style="color: <?php echo $status_color; ?>; font-weight: bold;">
                    <?php echo strtoupper($status); ?>
                  </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p style="text-align: center; padding: 50px; color: white;">
          No borrowing activity yet. 
          <a href="public-homepage.php" style="color: #4CAF50;">Browse books</a>
        </p>
      <?php endif; ?>
      
    </section>
  </main>
</body>
<section><?php include('footer.php');?></section>
</html>

<?php
mysqli_stmt_close($stmt_recent);
mysqli_close($conn);
*/
?>
