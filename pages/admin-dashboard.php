<?php 
// pages/admin-dashboard.php - Complete Admin Dashboard with Tracking

// Include admin authentication check (ensures only logged-in admins can access)
include('../config/admin-auth.php');

// Include database connection file
include('../config/database.php'); 

// ===== HANDLE MARK AS RETURNED ACTION =====
// Check if the form was submitted AND the "mark_returned" button was clicked
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_returned'])) {
    
// Get the borrow_id from the form (sanitize to prevent SQL injection)
$borrow_id = filter_input(INPUT_POST, "borrow_id", FILTER_SANITIZE_NUMBER_INT);

// Check if borrow_id is not empty
if(!empty($borrow_id)) {
    
    // SQL query to get book details before marking as returned
    // We need book_id to restore quantity
    $check_sql = "SELECT book_id FROM borrow_records WHERE borrow_id = ? AND status = 'borrowed'";
    
    // Prepare the SQL statement for security
    $check_stmt = mysqli_prepare($conn, $check_sql);
    
    // Check if preparation was successful
    if($check_stmt) {
        // Bind the parameter (i = integer)
        mysqli_stmt_bind_param($check_stmt, "i", $borrow_id);
        
        // Execute the query
        mysqli_stmt_execute($check_stmt);
        
        // Get the result
        $result = mysqli_stmt_get_result($check_stmt);
        
        // Check if a record was found
        if($row = mysqli_fetch_assoc($result)) {
          
          // Store the book_id
          $book_id = $row['book_id'];
          
          // Get today's date for date_returned
          $date_returned = date('Y-m-d');
          
          // SQL query to update the borrow record status to 'returned'
          $update_sql = "UPDATE borrow_records SET status = 'returned', date_returned = ? WHERE borrow_id = ?";
          
          // Prepare the update statement
          $update_stmt = mysqli_prepare($conn, $update_sql);
          
          // Check if preparation was successful
          if($update_stmt) {
              // Bind parameters (s = string for date, i = integer for borrow_id)
              mysqli_stmt_bind_param($update_stmt, "si", $date_returned, $borrow_id);
              
              // Execute the update query
              if(mysqli_stmt_execute($update_stmt)) {
                  
                  // Update successful! Now restore book quantity
                  // SQL query to increase quantity by 1
                  $restore_sql = "UPDATE books SET quantity = quantity + 1 WHERE bookId = ?";
                  
                  // Prepare the restore statement
                  $restore_stmt = mysqli_prepare($conn, $restore_sql);
                  
                  // Bind parameter (i = integer)
                  mysqli_stmt_bind_param($restore_stmt, "i", $book_id);
                  
                  // Execute the query
                  mysqli_stmt_execute($restore_stmt);
                  
                  // Close the restore statement
                  mysqli_stmt_close($restore_stmt);
                  
                  // Set success message (emoji removed - CSS adds styled icon)
                  $_SESSION['success_message'] = "Book marked as returned successfully!";
              } else {
                  // Update failed
                  $_SESSION['error_message'] = "Error marking book as returned.";
              }
              
              // Close the update statement
              mysqli_stmt_close($update_stmt);
          }
      } else {
          // No record found
          $_SESSION['error_message'] = "Borrow record not found.";
      }
        
        // Close the check statement
        mysqli_stmt_close($check_stmt);
    }
}

// Redirect to same page to prevent form resubmission
header("Location: " . $_SERVER['PHP_SELF']);
exit();
}

// ===== GET STATISTICS FOR DASHBOARD CARDS =====

// 1. Get total number of unique visitors (count of users)
$sql_visitors = "SELECT COUNT(DISTINCT userId) as total_visitors FROM users";
// Execute the query
$result_visitors = mysqli_query($conn, $sql_visitors);
// Fetch the result as an associative array
$visitors = mysqli_fetch_assoc($result_visitors);
// Store the count in a variable (default to 0 if no result)
$total_visitors = $visitors['total_visitors'] ?? 0;

// 2. Get total number of currently borrowed books (status = 'borrowed')
$sql_borrowed = "SELECT COUNT(*) as total_borrowed FROM borrow_records WHERE status = 'borrowed'";
// Execute the query
$result_borrowed = mysqli_query($conn, $sql_borrowed);
// Fetch the result
$borrowed = mysqli_fetch_assoc($result_borrowed);
// Store the count
$total_borrowed = $borrowed['total_borrowed'] ?? 0;

// 3. Get total number of unreturned books that are PAST due date
$sql_unreturned = "SELECT COUNT(*) as total_unreturned 
                   FROM borrow_records 
                   WHERE status = 'borrowed' AND due_date < CURDATE()";
// CURDATE() gets today's date, so this finds books overdue
// Execute the query
$result_unreturned = mysqli_query($conn, $sql_unreturned);
// Fetch the result
$unreturned = mysqli_fetch_assoc($result_unreturned);
// Store the count
$total_unreturned = $unreturned['total_unreturned'] ?? 0;

// ===== GET RECENTLY BORROWED BOOKS WITH USER INFO =====
// SQL query to get borrowed books with user information
// JOIN combines data from borrow_records, books, and users tables
$sql_recent = "SELECT 
    br.borrow_id,
    br.date_borrowed,
    br.due_date,
    br.status,
    u.username as borrower_name,
    b.book_title,
    b.bookId,
    b.isbn,
    b.book_category
FROM borrow_records br
JOIN users u ON br.user_id = u.userId
JOIN books b ON br.book_id = b.bookId
WHERE br.status = 'borrowed'
ORDER BY br.date_borrowed DESC
LIMIT 10";
// LIMIT 10 means we only get the 10 most recent records

// Execute the query
$result_recent = mysqli_query($conn, $sql_recent);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../styles/admin-dashboard.css">
  <link rel="icon" href="../images/icons/bookIcon.png" type="image/png">
</head>
<body>

  <!-- Include the admin header (contains greeting and time) -->
  <header>
    <section>
      <?php include('admin-header.php');?>
    </section>
  </header>
  
  <main>
    <div class="main-container"> 

      <!-- Include the side navigation menu -->
      <?php include('admin-side-nav.php');?>

      <!-- SUCCESS/ERROR MESSAGES -->
      <?php if(isset($_SESSION['success_message'])): ?>
        <div class="dashboard-message success-message">
          <?php 
            // Display success message
            echo $_SESSION['success_message']; 
            // Remove message so it doesn't show again
            unset($_SESSION['success_message']);
          ?>
        </div>
      <?php endif; ?>

      <?php if(isset($_SESSION['error_message'])): ?>
        <div class="dashboard-message error-message">
          <?php 
            // Display error message
            echo $_SESSION['error_message']; 
            // Remove message
            unset($_SESSION['error_message']);
          ?>
        </div>
      <?php endif; ?>

      <!-- ===== STATISTICS CARDS SECTION ===== -->
      <section class="information-cards-grid">
        
        <!-- Card 1: Total Visitors -->
        <section class="information-card">
          <section class="card-title">
            <h2>Total Visitors</h2>
            <img src="../images/icons/visitor-icon.svg">
          </section>
          <section class="card-count">
            <!-- Display the total number of visitors -->
            <h1><?php echo $total_visitors; ?></h1>
          </section>     
        </section>

        <!-- Card 2: Currently Borrowed Books -->
        <section class="information-card">
          <section class="card-title">
            <h2>Borrowed Books</h2>
            <img src="../images/icons/borrow-icon.svg">
          </section>
          <section class="card-count">
            <!-- Display the total number of borrowed books -->
            <h1><?php echo $total_borrowed; ?></h1>
          </section>     
        </section>

        <!-- Card 3: Unreturned (Overdue) Books -->
        <section class="information-card">
          <section class="card-title">
            <h2>Unreturned Books</h2>
            <img src="../images/icons/timer-icon.svg">
          </section>
          <section class="card-count">
            <!-- Display count in RED if there are overdue books -->
            <h1 style="<?php echo $total_unreturned > 0 ? 'color: #ff4444;' : ''; ?>">
              <?php echo $total_unreturned; ?>
            </h1>
          </section>     
        </section>
      </section>

      <!-- ===== RECENTLY BORROWED BOOKS TABLE ===== -->
      <section class="information-table-container">
        <h2>Recently Borrowed Books:</h2>
        
        <!-- Check if there are any borrowed books -->
        <?php if(mysqli_num_rows($result_recent) > 0): ?>
          
          <div class="table-responsive">
            <table class="table-wrap">
              <thead>
                <tr>
                  <th>Borrower Name</th>
                  <th>Book Title</th>
                  <th>Book ID</th>
                  <th>Category</th>
                  <th>Date Borrowed</th>
                  <th>Due Date</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <!-- Loop through each borrowed book record -->
                <?php while($book = mysqli_fetch_assoc($result_recent)): ?>
                  <?php
                    // Get today's date
                    $today = date('Y-m-d');
                    
                    // Check if book is overdue
                    $is_overdue = ($book['due_date'] < $today);
                    
                    // Set status display text and color
                    if($is_overdue) {
                      $status_text = "OVERDUE";
                      $status_color = "color: #ff4444; font-weight: bold;";
                    } else {
                      $status_text = "BORROWED";
                      $status_color = "color: #4CAF50;";
                    }
                  ?>
                  <tr>
                    <!-- Display borrower's username -->
                    <td data-label="Borrower Name">
                      <?php echo htmlspecialchars($book['borrower_name']); ?>
                    </td>
                    
                    <!-- Display book title -->
                    <td data-label="Book Title">
                      <?php echo htmlspecialchars($book['book_title']); ?>
                    </td>
                    
                    <!-- Display book ID -->
                    <td data-label="Book ID">
                      <?php echo htmlspecialchars($book['bookId']); ?>
                    </td>
                    
                    <!-- Display book category -->
                    <td data-label="Category">
                      <?php echo htmlspecialchars($book['book_category']); ?>
                    </td>
                    
                    <!-- Display date borrowed (formatted) -->
                    <td data-label="Date Borrowed">
                      <?php echo date('M j, Y', strtotime($book['date_borrowed'])); ?>
                    </td>
                    
                    <!-- Display due date (formatted) -->
                    <td data-label="Due Date">
                      <?php echo date('M j, Y', strtotime($book['due_date'])); ?>
                    </td>
                    
                    <!-- Display status with color -->
                    <td data-label="Status" style="<?php echo $status_color; ?>">
                      <?php echo $status_text; ?>
                    </td>
                    
                    <!-- Action button to mark as returned -->
                    <td data-label="Action">
                      <form method="post" style="display: inline;">
                        <!-- Hidden input to send borrow_id when form is submitted -->
                        <input type="hidden" name="borrow_id" value="<?php echo $book['borrow_id']; ?>">
                        
                        <!-- Button to mark book as returned -->
                        <button type="submit" 
                                name="mark_returned" 
                                class="btn-mark-returned"
                                onclick="return confirm('Mark this book as returned?')">
                          ✓ Returned
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
          
        <?php else: ?>
          <!-- Display message if no books are currently borrowed -->
          <p style="text-align: center; padding: 50px; color: white;">
            No books are currently borrowed.
          </p>
        <?php endif; ?>
        
      </section>
    </div>
  </main>

  <!-- JavaScript for auto-hiding messages -->
  <script>
    // Wait for page to fully load
    window.addEventListener('DOMContentLoaded', function() {
      // Get success and error message elements
      const successMsg = document.querySelector('.success-message');
      const errorMsg = document.querySelector('.error-message');
      
      // If success message exists
      if (successMsg) {
        // After 5 seconds (5000 milliseconds)
        setTimeout(function() {
          // Fade out the message
          successMsg.style.opacity = '0';
          // After fade animation (300ms), hide completely
          setTimeout(function() {
            successMsg.style.display = 'none';
          }, 300);
        }, 5000);
      }
      
      // Same for error message
      if (errorMsg) {
        setTimeout(function() {
          errorMsg.style.opacity = '0';
          setTimeout(function() {
            errorMsg.style.display = 'none';
          }, 300);
        }, 5000);
      }
    });
  </script>

</body>
<!-- Include footer -->
<section><?php include('footer.php');?></section>
</html>

<?php
// Close database connection to free up resources
mysqli_close($conn);
?>