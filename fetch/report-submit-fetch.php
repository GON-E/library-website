<?php
// Ensure database connection and user session are initialized
include('../config/database.php');
include('../config/user-auth.php');

// Define status messages
$response = ['status' => 'error', 'message' => 'Invalid request method'];

// Check if form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Determine user id from available session keys
    $user_id = null;
    if (!empty($_SESSION['user_id'])) $user_id = $_SESSION['user_id'];
    elseif (!empty($_SESSION['userId'])) $user_id = $_SESSION['userId'];
    elseif (!empty($_SESSION['userId'])) $user_id = $_SESSION['userId'];

    // Determine user name: check multiple session keys used by different pages
    if (!empty($_SESSION['user_name'])) {
        $user_name = $_SESSION['user_name'];
    } elseif (!empty($_SESSION['userName'])) {
        $user_name = $_SESSION['userName'];
    } elseif (!empty($_SESSION['username'])) {
        $user_name = $_SESSION['username'];
    } elseif (!empty($_SESSION['user_email'])) {
        $user_name = $_SESSION['user_email'];
    } elseif (!empty($_SESSION['userEmail'])) {
        $user_name = $_SESSION['userEmail'];
    } else {
        $user_name = 'Anonymous';
    }

    // If not logged in (no user id), return error
    if (empty($user_id)) {
        $response['message'] = "User must be logged in to submit a report.";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    // Get form data and sanitize/trim
    $issue = isset($_POST['issue']) ? trim($_POST['issue']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Validate input
    if (empty($issue) || empty($description)) {
        $response['message'] = "Please select an issue type and provide a description.";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    // --- Database Initialization (create reports table if missing) ---
    // Avoid foreign key constraints here to prevent failures when 'users' table differs
    $create_table_sql = "
        CREATE TABLE IF NOT EXISTS reports (
            report_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            user_name VARCHAR(100) NOT NULL,
            issue_type VARCHAR(100) NOT NULL,
            description LONGTEXT NOT NULL,
            status VARCHAR(50) DEFAULT 'Pending',
            report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    if (!mysqli_query($conn, $create_table_sql)) {
        $response['message'] = "Database error: Could not ensure reports table exists.";
        error_log("Error creating reports table: " . mysqli_error($conn));
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    // --- Insert Report Data ---
    $insert_sql = "INSERT INTO reports (user_id, user_name, issue_type, description, status) 
                    VALUES (?, ?, ?, ?, 'Pending')";
    
    $stmt = mysqli_prepare($conn, $insert_sql);
    
    if ($stmt === false) {
        $response['message'] = "Database error: Failed to prepare statement.";
        error_log("Error preparing statement: " . mysqli_error($conn));
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    
    // Bind parameters: (i)nteger, (s)tring, (s)tring, (s)tring
    mysqli_stmt_bind_param($stmt, "isss", $user_id, $user_name, $issue, $description);
    
    // Execute statement
    if (mysqli_stmt_execute($stmt)) {
        $response['status'] = 'success';
        $response['message'] = "Report submitted successfully. We will look into it shortly.";
        mysqli_stmt_close($stmt);
    } else {
        $response['message'] = "Error submitting report: " . mysqli_stmt_error($stmt);
        error_log($response['message']);
        mysqli_stmt_close($stmt);
    }

    // Send JSON response back to the client (for AJAX submission)
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();

} else {
    // If not a POST request
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>