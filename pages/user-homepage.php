<?php
session_start();
include('../config/database.php');

// Check if user is logged in
if(!isset($_SESSION['userId'])) {
    header("Location: ../pages/user-login.php");
    exit();
}

$user_id = $_SESSION['userId'];

// Get borrowed books - using isbn as the join key
$sql = "SELECT bb.*, b.book_title, b.author, b.image, b.isbn 
        FROM borrow_book bb
        JOIN books b ON bb.book_id = b.isbn
        WHERE bb.user_id = ?
        ORDER BY bb.date_borrowed DESC";

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
    .borrowed-books-container {
      margin: 20px auto;
      max-width: 1200px;
      padding: 20px;
      margin-left: 60px;
      background-color: #2f3e2a;
      border-radius: 10px;
    }
    
    .borrowed-books-container h2 {
      color: white;
      text-align: center;
      margin-bottom: 20px;
    }
    
    .borrowed-book-card {
      background-color: white;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 8px;
      display: grid;
      grid-template-columns: 100px 1fr 200px;
      gap: 15px;
      align-items: center;
    }
    
    .borrowed-book-card img {
      width: 100px;
      height: 140px;
      object-fit: cover;
      border-radius: 5px;
    }
    
    .book-info h3 {
      margin: 0 0 5px 0;
      color: #3f7f45;
    }
    
    .book-info p {
      margin: 5px 0;
      font-size: 14px;
      color: #666;
    }
    
    .status-info {
      text-align: right;
    }
    
    .status-badge {
      display: inline-block;
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: bold;
      margin-bottom: 10px;
    }
    
    .status-borrowed {
      background-color: #4CAF50;
      color: white;
    }
    
    .status-overdue {
      background-color: #f44336;
      color: white;
    }
    
    .status-returned {
      background-color: #999;
      color: white;
    }
    
    .browse-link {
      display: inline-block;
      margin: 20px;
      margin-left: 60px;
      padding: 10px 20px;
      background-color: #3f7f45;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
    
    .browse-link:hover {
      background-color: #2e5e32;
    }

    .no-books-message {
      color: white;
      text-align: center;
      padding: 40px;
    }

    .no-books-message a {
      color: #4CAF50;
      text-decoration: underline;
    }

    @media screen and (max-width: 768px) {
      .borrowed-book-card {
        grid-template-columns: 1fr;
        text-align: center;
      }

      .borrowed-book-card img {
        margin: 0 auto;
      }

      .status-info {
        text-align: center;
      }

      .borrowed-books-container {
        margin-left: 20px;
        margin-right: 20px;
      }

      .browse-link {
        margin-left: 20px;
      }
    }
  </style>
</head>
<body>

  <header><?php include('public-header.php'); ?></header>
  <section><?php include('public-nav.php'); ?></section>

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