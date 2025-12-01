<?php
session_start();
include('../config/database.php');

// Check if user is logged in
if(!isset($_SESSION['userId'])) {
    header("Location: ../pages/user-login.php");
    exit();
}

$user_id = $_SESSION['userId'];

// Get borrowed books - join with books table using bookId
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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Borrowed Books</title>
  <link rel="stylesheet" href="../styles/admin-dashboard.css">
  <style>

  </style>
</head>
<body>

  <header><?php include('public-header.php'); ?></header>
  <section><?php include('user-nav.php'); ?></section>

  <a href="public-homepage.php" class="browse-link">📚 Browse All Books</a>

  <div class="borrowed-books-container">
    <h2>My Borrowed Books</h2>
    
    <?php if(mysqli_num_rows($result) > 0): ?>
      <?php while($book = mysqli_fetch_assoc($result)): ?>
        <?php
          // Determine status
          $status = $book['status'];
          $today = date('Y-m-d');
          
          if($status == 'borrowed' && $book['due_date'] < $today) {
            $status = 'overdue';
          }
          
          $status_class = 'status-' . $status;
        ?>
        
        <div class="borrowed-book-card">
          <div>
            <?php if(!empty($book['image'])): ?>
              <img src="../images/books/<?php echo htmlspecialchars($book['image']); ?>" 
                   alt="<?php echo htmlspecialchars($book['book_title']); ?>">
            <?php else: ?>
              <div style="width: 100px; height: 140px; background: #eee; display: flex; align-items: center; justify-content: center; border-radius: 5px;">No Image</div>
            <?php endif; ?>
          </div>
          
          <div class="book-info">
            <h3><?php echo htmlspecialchars($book['book_title']); ?></h3>
            <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
            <p><strong>ISBN:</strong> <?php echo htmlspecialchars($book['isbn']); ?></p>
            <p><strong>Date Borrowed:</strong> <?php echo date('F j, Y', strtotime($book['date_borrowed'])); ?></p>
            <p><strong>Due Date:</strong> <?php echo date('F j, Y', strtotime($book['due_date'])); ?></p>
            <?php if($book['date_returned']): ?>
              <p><strong>Returned:</strong> <?php echo date('F j, Y', strtotime($book['date_returned'])); ?></p>
            <?php endif; ?>
          </div>
          
          <div class="status-info">
            <span class="status-badge <?php echo $status_class; ?>">
              <?php echo strtoupper($status); ?>
            </span>
            <?php if($status == 'borrowed' || $status == 'overdue'): ?>
              <br>
              <?php
                $days_left = ceil((strtotime($book['due_date']) - time()) / 86400);
                if($days_left > 0) {
                  echo "<p style='color: #4CAF50;'>$days_left days left</p>";
                } else {
                  echo "<p style='color: #f44336;'>" . abs($days_left) . " days overdue</p>";
                }
              ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="no-books-message">
        You haven't borrowed any books yet.<br><br>
        <a href="public-homepage.php">Click here to browse available books</a>
      </p>
    <?php endif; ?>
  </div>

</body>
<section><?php include('footer.php'); ?></section>
</html>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>