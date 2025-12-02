<?php 
// fetch/user-login-fetch.php - Handles user login with secure password verification

// Start the session at the very beginning (check if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load database connection
include('../config/database.php');

// Initialize error message variable
$error_message = "";

// Check if form was submitted using POST method
if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get email from form and sanitize it
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    // Get password from form without filtering (passwords need exact matching)
    $password = filter_input(INPUT_POST, "password", FILTER_UNSAFE_RAW);
    
    // Validate that email is not empty
    if(!$email) {
      $error_message = "Email is missing.";
    } else if(empty($password)) {
      // Validate that password is not empty
      $error_message = "Password is missing.";
    } else {
        
        // Prepare SQL query to find user by email (get userId, username, and password hash)
        $sql = "SELECT userId, username, password FROM users WHERE email = ?";
        
        // Prepare statement to prevent SQL injection
        if ($stmt = $conn->prepare($sql)) {
            
            // Bind the email variable to the placeholder ('s' = string type)
            $stmt->bind_param("s", $email);
            
            // Execute the prepared statement
            if ($stmt->execute()) {
                
                // Get the result from the query
                $result = $stmt->get_result();

                // Check if exactly 1 user was found (should be unique by email)
                if($result->num_rows === 1) { 
                    // Fetch the user data as an associative array
                    $row = $result->fetch_assoc();
                    // Get the stored hashed password from database
                    $storedPassword = $row['password']; 
                    
                    // Use password_verify to securely check if entered password matches the hash
                    if(password_verify($password, $storedPassword)) { 
                        // PASSWORD CORRECT! Store user info in session
                        $_SESSION['userId'] = $row['userId']; // Store user ID
                        $_SESSION['userName'] = $row['username']; // Store username

                        // Redirect to user homepage (books page)
                        header("location: user-homepage.php");
                        exit();

                    } else {
                        // Password does not match the hash
                        $error_message = "Incorrect Email or Password!";
                    }

                } else {
                    // No user found or more than 1 found (should not happen with UNIQUE email)
                    $error_message = "Incorrect Email or Password!";
                }

            } else {
                // SQL query execution failed
                $error_message = "Error occurred during query execution.";
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            // SQL statement preparation failed
            $error_message = "Database error: Could not prepare statement.";
        }
    }
}
// Note: Database connection is typically closed in the database config file, not here
?>