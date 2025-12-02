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

// ===== HANDLE CANCEL BORROW ACTION =====
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_borrow'])) {
    $borrow_id = filter_input(INPUT_POST, "borrow_id", FILTER_SANITIZE_NUMBER_INT);
    
    if(!empty($borrow_id)) {
        // Get borrow record details
        $check_sql = "SELECT br.book_id, b.book_title 
                      FROM borrow_records br 
                      JOIN books b ON br.book_id = b.bookId 
                      WHERE br.borrow_id = ? AND br.user_id = ? AND br.status = 'borrowed'";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        
        if($check_stmt) {
            mysqli_stmt_bind_param($check_stmt, "ii", $borrow_id, $user_id);
            mysqli_stmt_execute($check_stmt);
            $result = mysqli_stmt_get_result($check_stmt);
            
            if($row = mysqli_fetch_assoc($result)) {
                $book_id = $row['book_id'];
                $book_title = $row['book_title'];
                
                // Delete the borrow record
                $delete_sql = "DELETE FROM borrow_records WHERE borrow_id = ? AND user_id = ?";
                $delete_stmt = mysqli_prepare($conn, $delete_sql);
                
                if($delete_stmt) {
                    mysqli_stmt_bind_param($delete_stmt, "ii", $borrow_id, $user_id);
                    
                    if(mysqli_stmt_execute($delete_stmt)) {
                        // Increase book quantity back
                        $update_sql = "UPDATE books SET quantity = quantity + 1 WHERE bookId = ?";
                        $update_stmt = mysqli_prepare($conn, $update_sql);
                        mysqli_stmt_bind_param($update_stmt, "i", $book_id);
                        mysqli_stmt_execute($update_stmt);
                        mysqli_stmt_close($update_stmt);
                        
                        $_SESSION['success_message'] = "✅ Borrow cancelled! '{$book_title}' has been returned to the library.";
                    } else {
                        $_SESSION['error_message'] = "❌ Error cancelling borrow. Please try again.";
                    }
                    mysqli_stmt_close($delete_stmt);
                }
            } else {
                $_SESSION['error_message'] = "❌ Borrow record not found or already processed.";
            }
            mysqli_stmt_close($check_stmt);
        }
    }
    
    // Redirect to refresh the page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

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

  <!-- Success/Error Messages -->
  <?php if(isset($_SESSION['success_message'])): ?>
    <div class="dashboard-message success-message">
      <?php 
        echo $_SESSION['success_message']; 
        unset($_SESSION['success_message']);
      ?>
    </div>
  <?php endif; ?>

  <?php if(isset($_SESSION['error_message'])): ?>
    <div class="dashboard-message error-message">
      <?php 
        echo $_SESSION['error_message']; 
        unset($_SESSION['error_message']);
      ?>
    </div>
  <?php endif; ?>

  <!-- Cancel Confirmation Modal -->
  <div id="cancelModal" class="cancel-modal-overlay">
    <div class="cancel-modal">
      <button class="modal-close" onclick="closeCancelModal()">&times;</button>
      
      <div class="modal-header">
        <h2>⚠️ Cancel Book Borrow?</h2>
      </div>
      
      <div class="modal-body">
        <p class="warning-text">
          Are you sure you want to cancel borrowing this book?
        </p>
        
        <div class="book-info-display">
          <div class="info-label">Book Title:</div>
          <div class="info-value" id="cancel-book-title"></div>
          
          <div class="info-label">Author:</div>
          <div class="info-value" id="cancel-book-author"></div>
          
          <div class="info-label">Date Borrowed:</div>
          <div class="info-value" id="cancel-date-borrowed"></div>
        </div>
        
        <p class="note-text">
          📌 The book will be returned to the library and become available for others to borrow.
        </p>
      </div>
      
      <form method="post" class="modal-footer">
        <input type="hidden" name="borrow_id" id="cancel-borrow-id">
        <button type="submit" name="cancel_borrow" class="btn-cancel-confirm">Yes, Cancel Borrow</button>
        <button type="button" onclick="closeCancelModal()" class="btn-keep">No, Keep It</button>
      </form>
    </div>
  </div>

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
          <div class="table-responsive">
            <table class="table-wrap">
              <thead>
                <tr>
                  <th>Book Title</th>
                  <th>Author</th>
                  <th>Date Borrowed</th>
                  <th>Due Date</th>
                  <th>Status</th>
                  <th>Days Remaining</th>
                  <th>Action</th>
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
                    <td data-label="Book Title"><?php echo htmlspecialchars($book['book_title']); ?></td>
                    <td data-label="Author"><?php echo htmlspecialchars($book['author']); ?></td>
                    <td data-label="Date Borrowed"><?php echo date('M j, Y', strtotime($book['date_borrowed'])); ?></td>
                    <td data-label="Due Date"><?php echo date('M j, Y', strtotime($book['due_date'])); ?></td>
                    <td data-label="Status" <?php echo $status_class; ?>><?php echo strtoupper($status); ?></td>
                    <td data-label="Days Remaining">
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
                    <td data-label="Action">
                      <?php if($status == 'borrowed'): ?>
                        <button type="button" 
                                class="btn-cancel-borrow"
                                onclick='openCancelModal(<?php echo json_encode([
                                    "borrowId" => $book['borrow_id'],
                                    "title" => $book['book_title'],
                                    "author" => $book['author'],
                                    "dateBorrowed" => date('M j, Y', strtotime($book['date_borrowed']))
                                ]); ?>)'>
                          🗑️ Cancel
                        </button>
                      <?php else: ?>
                        <span style="color: #999;">-</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
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

  <script src="../script/user-dashboard.js"></script>

</body>
<section><?php include('footer.php'); ?></section>
</html>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>