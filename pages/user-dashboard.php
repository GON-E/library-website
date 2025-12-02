<?php
// pages/user-dashboard.php - MY BORROWED BOOKS (Dashboard View)
// Start the session to track user login
session_start();

// Include database connection file
include('../config/database.php');

// Check if user is logged in by verifying session variable exists
if(!isset($_SESSION['userId'])) {
    // If not logged in, redirect to login page
    header("Location: ../pages/user-login.php");
    exit(); // Stop script execution after redirect
}

// Get the logged-in user's ID from session
$user_id = $_SESSION['userId'];

// ===== HANDLE CANCEL BORROW ACTION =====
// Check if form was submitted via POST method AND cancel button was clicked
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_borrow'])) {
    // Sanitize the borrow_id input to prevent SQL injection
    $borrow_id = filter_input(INPUT_POST, "borrow_id", FILTER_SANITIZE_NUMBER_INT);
    
    // Verify that borrow_id is not empty
    if(!empty($borrow_id)) {
        // SQL query to get borrow record details before deletion
        // We need book_id to restore quantity and book_title for success message
        $check_sql = "SELECT br.book_id, b.book_title 
                      FROM borrow_records br 
                      JOIN books b ON br.book_id = b.bookId 
                      WHERE br.borrow_id = ? AND br.user_id = ? AND br.status = 'borrowed'";
        
        // Prepare the SQL statement for security
        $check_stmt = mysqli_prepare($conn, $check_sql);
        
        // Check if statement preparation was successful
        if($check_stmt) {
            // Bind the parameters (ii = two integers)
            mysqli_stmt_bind_param($check_stmt, "ii", $borrow_id, $user_id);
            
            // Execute the query
            mysqli_stmt_execute($check_stmt);
            
            // Get the result set
            $result = mysqli_stmt_get_result($check_stmt);
            
            // Check if a record was found
            if($row = mysqli_fetch_assoc($result)) {
                // Store book_id and title for later use
                $book_id = $row['book_id'];
                $book_title = $row['book_title'];
                
                // SQL query to delete the borrow record
                $delete_sql = "DELETE FROM borrow_records WHERE borrow_id = ? AND user_id = ?";
                
                // Prepare the delete statement
                $delete_stmt = mysqli_prepare($conn, $delete_sql);
                
                // Check if preparation was successful
                if($delete_stmt) {
                    // Bind parameters (ii = two integers)
                    mysqli_stmt_bind_param($delete_stmt, "ii", $borrow_id, $user_id);
                    
                    // Execute the delete query
                    if(mysqli_stmt_execute($delete_stmt)) {
                        // Delete successful! Now restore book quantity
                        // SQL query to increase quantity by 1
                        $update_sql = "UPDATE books SET quantity = quantity + 1 WHERE bookId = ?";
                        
                        // Prepare the update statement
                        $update_stmt = mysqli_prepare($conn, $update_sql);
                        
                        // Bind parameter (i = integer)
                        mysqli_stmt_bind_param($update_stmt, "i", $book_id);
                        
                        // Execute the update query
                        mysqli_stmt_execute($update_stmt);
                        
                        // Close the update statement
                        mysqli_stmt_close($update_stmt);
                        
                        // Set success message in session (will display after redirect)
                        $_SESSION['success_message'] = "✅ Borrow cancelled! '{$book_title}' has been returned to the library.";
                    } else {
                        // Delete failed - set error message
                        $_SESSION['error_message'] = "❌ Error cancelling borrow. Please try again.";
                    }
                    
                    // Close the delete statement
                    mysqli_stmt_close($delete_stmt);
                }
            } else {
                // No record found - set error message
                $_SESSION['error_message'] = "❌ Borrow record not found or already processed.";
            }
            
            // Close the check statement
            mysqli_stmt_close($check_stmt);
        }
    }
    
    // Redirect to same page to refresh and prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit(); // Stop script execution after redirect
}

// ===== GET BORROWED BOOKS STATISTICS =====
// SQL query to count different borrow statuses
// COUNT with CASE counts only rows matching the condition
$sql_stats = "SELECT 
    COUNT(CASE WHEN status = 'borrowed' THEN 1 END) as active_borrows,
    COUNT(CASE WHEN status = 'borrowed' AND due_date < CURDATE() THEN 1 END) as overdue_count,
    COUNT(*) as total_borrowed
    FROM borrow_records 
    WHERE user_id = ?";

// Prepare the statistics query
$stmt_stats = mysqli_prepare($conn, $sql_stats);

// Bind user_id parameter (i = integer)
mysqli_stmt_bind_param($stmt_stats, "i", $user_id);

// Execute the query
mysqli_stmt_execute($stmt_stats);

// Get result set
$stats_result = mysqli_stmt_get_result($stmt_stats);

// Fetch statistics as associative array
$stats = mysqli_fetch_assoc($stats_result);

// Close the statistics statement
mysqli_stmt_close($stmt_stats);

// ===== GET ALL BORROWED BOOKS =====
// SQL query to get borrow records with book details
// JOIN combines data from borrow_records and books tables
$sql = "SELECT br.*, b.book_title, b.author, b.image, b.isbn 
        FROM borrow_records br
        JOIN books b ON br.book_id = b.bookId
        WHERE br.user_id = ?
        ORDER BY br.date_borrowed DESC";

// Prepare the main query
$stmt = mysqli_prepare($conn, $sql);

// Bind user_id parameter
mysqli_stmt_bind_param($stmt, "i", $user_id);

// Execute the query
mysqli_stmt_execute($stmt);

// Get result set
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

  <!-- Include header (contains user greeting and time) -->
  <header><?php include('user-header.php'); ?></header>
  
  <!-- Include navigation sidebar -->
  <section><?php include('user-nav.php'); ?></section>

  <!-- SUCCESS MESSAGE DISPLAY -->
  <?php if(isset($_SESSION['success_message'])): ?>
    <div class="dashboard-message success-message">
      <?php 
        // Display the success message
        echo $_SESSION['success_message']; 
        // Remove message from session so it doesn't show again
        unset($_SESSION['success_message']);
      ?>
    </div>
  <?php endif; ?>

  <!-- ERROR MESSAGE DISPLAY -->
  <?php if(isset($_SESSION['error_message'])): ?>
    <div class="dashboard-message error-message">
      <?php 
        // Display the error message
        echo $_SESSION['error_message']; 
        // Remove message from session
        unset($_SESSION['error_message']);
      ?>
    </div>
  <?php endif; ?>

  <!-- CANCEL CONFIRMATION MODAL -->
  <div id="cancelModal" class="cancel-modal-overlay">
    <div class="cancel-modal">
      <!-- Close button (X) -->
      <button class="modal-close" onclick="closeCancelModal()">&times;</button>
      
      <!-- Modal header -->
      <div class="modal-header">
        <h2>⚠️ Cancel Book Borrow?</h2>
      </div>
      
      <!-- Modal body with book information -->
      <div class="modal-body">
        <p class="warning-text">
          Are you sure you want to cancel borrowing this book?
        </p>
        
        <!-- Display book details in modal -->
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
      
      <!-- Modal footer with action buttons -->
      <form method="post" class="modal-footer">
        <!-- Hidden field to send borrow_id when form is submitted -->
        <input type="hidden" name="borrow_id" id="cancel-borrow-id">
        
        <!-- Submit button to confirm cancellation -->
        <button type="submit" name="cancel_borrow" class="btn-cancel-confirm">Yes, Cancel Borrow</button>
        
        <!-- Button to close modal without cancelling -->
        <button type="button" onclick="closeCancelModal()" class="btn-keep">No, Keep It</button>
      </form>
    </div>
  </div>

  <main>
    <div class="main-container">
      
      <!-- STATISTICS CARDS SECTION -->
      <section class="information-cards-grid">
        
        <!-- Card 1: Currently Borrowed -->
        <section class="information-card">
          <section class="card-title">
            <h2>Currently Borrowed</h2>
            <img src="../images/icons/borrow-icon.svg">
          </section>
          <section class="card-count">
            <!-- Display count of active borrows -->
            <h1><?php echo $stats['active_borrows']; ?></h1>
          </section>
        </section>

        <!-- Card 2: Overdue Books -->
        <section class="information-card">
          <section class="card-title">
            <h2>Overdue Books</h2>
            <img src="../images/icons/timer-icon.svg">
          </section>
          <section class="card-count">
            <!-- Display overdue count in red if > 0 -->
            <h1 style="<?php echo $stats['overdue_count'] > 0 ? 'color: #ff4444;' : ''; ?>">
              <?php echo $stats['overdue_count']; ?>
            </h1>
          </section>
        </section>

        <!-- Card 3: Total Borrowed -->
        <section class="information-card">
          <section class="card-title">
            <h2>Total Borrowed</h2>
            <img src="../images/icons/visitor-icon.svg">
          </section>
          <section class="card-count">
            <!-- Display total borrow count -->
            <h1><?php echo $stats['total_borrowed']; ?></h1>
          </section>
        </section>
      </section>

      <!-- BORROWED BOOKS TABLE SECTION -->
      <section class="information-table-container">
        <h2>My Borrowed Books</h2>
        
        <!-- Check if user has borrowed books -->
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
                <!-- Loop through each borrowed book -->
                <?php while($book = mysqli_fetch_assoc($result)): ?>
                  <?php
                    // Get status from database
                    $status = $book['status'];
                    
                    // Get today's date
                    $today = date('Y-m-d');
                    
                    // Calculate days remaining until due date
                    // ceil() rounds up, time() gets current timestamp, 86400 = seconds in a day
                    $days_left = ceil((strtotime($book['due_date']) - time()) / 86400);
                    
                    // Check if book is overdue
                    if($status == 'borrowed' && $book['due_date'] < $today) {
                      $status = 'overdue'; // Change status to overdue
                    }
                    
                    // Set CSS style based on status
                    $status_class = $status == 'overdue' ? 'style="color: #ff4444; font-weight: bold;"' : 
                                   ($status == 'returned' ? 'style="color: #4CAF50;"' : '');
                  ?>
                  <tr>
                    <!-- Display book title -->
                    <td data-label="Book Title"><?php echo htmlspecialchars($book['book_title']); ?></td>
                    
                    <!-- Display author name -->
                    <td data-label="Author"><?php echo htmlspecialchars($book['author']); ?></td>
                    
                    <!-- Display date borrowed (formatted) -->
                    <td data-label="Date Borrowed"><?php echo date('M j, Y', strtotime($book['date_borrowed'])); ?></td>
                    
                    <!-- Display due date (formatted) -->
                    <td data-label="Due Date"><?php echo date('M j, Y', strtotime($book['due_date'])); ?></td>
                    
                    <!-- Display status with color coding -->
                    <td data-label="Status" <?php echo $status_class; ?>><?php echo strtoupper($status); ?></td>
                    
                    <!-- Display days remaining or overdue -->
                    <td data-label="Days Remaining">
                      <?php 
                        if($status == 'returned') {
                          // Show dash for returned books
                          echo '-';
                        } elseif($days_left > 0) {
                          // Show days left in green
                          echo "<span style='color: #4CAF50;'>$days_left days</span>";
                        } else {
                          // Show days late in red
                          echo "<span style='color: #ff4444;'>" . abs($days_left) . " days late</span>";
                        }
                      ?>
                    </td>
                    
                    <!-- Display cancel button only for borrowed books -->
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
                        <!-- Show dash for returned/overdue books -->
                        <span style="color: #999;">-</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <!-- Show message if no books borrowed -->
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

  <!-- Include JavaScript for modal functionality -->
  <script src="../script/user-dashboard.js"></script>

</body>

<!-- Include footer -->
<section><?php include('footer.php'); ?></section>
</html>

<?php
// Close the main statement
mysqli_stmt_close($stmt);

// Close database connection
mysqli_close($conn);
?>