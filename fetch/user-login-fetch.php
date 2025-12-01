<?php 
// 1. Start the session at the very beginning of the script
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// NOTE: Adjust the path if necessary
include('../config/database.php');

$error_message = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Input Filtering (Securely get and sanitize data)
    // Sanitize email input
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    // Use RAW for password, as sanitizing might alter the input needed for verification
    $password = filter_input(INPUT_POST, "password", FILTER_UNSAFE_RAW);
    
    // 3. Basic Validation
    if(!$email) {
      $error_message = "Email is missing.";
    } else if(empty($password)) {
      $error_message = "Password is missing.";
    } else {
        
        // 4. PREPARE THE SQL STATEMENT (Security Step 1)
        // Only select the data needed: id, username, and the hashed password
        $sql = "SELECT userId, username, password FROM users WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            
            // 5. BIND PARAMETER (Security Step 2)
            // 's' specifies the variable type is string
            $stmt->bind_param("s", $email);
            
            // 6. EXECUTE THE STATEMENT
            if ($stmt->execute()) {
                
                // 7. GET THE RESULT
                $result = $stmt->get_result();

                if($result->num_rows === 1) { 
                    $row = $result->fetch_assoc();
                    $storedPassword = $row['password']; 
                    
                    // 8. VERIFY THE PASSWORD HASH
                    if(password_verify($password, $storedPassword)) { 
                        // SUCCESS! Start the session variables
                        $_SESSION['userId'] = $row['userId']; // Use 'id' from DB column
                        $_SESSION['userName'] = $row['username'];

                        // Redirect to user homepage (borrowed books page)
                        header("location: user-homepage.php");
                        exit();

                    } else {
                        $error_message = "Incorrect Email or Password!";
                        // NOTE: It is better to use a generic message for security
                    }

                } else {
                    // This handles both 0 and >1 results (though >1 should be prevented by a UNIQUE index on email)
                    $error_message = "Incorrect Email or Password!";
                }

            } else {
                // Handle execution error
                $error_message = "Error occurred during query execution.";
            }

            // Close the statement
            $stmt->close();
        } else {
            // Handle preparation error
            $error_message = "Database error: Could not prepare statement.";
        }
    }
}
// NOTE: $conn->close() is typically done at the end of the script or connection file, not here.
?>