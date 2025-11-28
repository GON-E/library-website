<?php 
// Include the database connection
// NOTE: Adjust the path if necessary
include("../config/database.php");

// Initialize an empty message variable
$message = "";

// Check if the form was submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize the email input
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    
    // Check if email is provided
    if(!$email) {
        $message = "Please enter your email address.";
    } else {
        // --- Step 1: Check if the user exists ---
        $sql_check = "SELECT id FROM users WHERE email = ?";
        
        // Use prepared statements to prevent SQL Injection
        if ($stmt = $conn->prepare($sql_check)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // User found, proceed to generate and store the token
                $user_row = $result->fetch_assoc();
                $user_id = $user_row['id'];
                
                // --- Step 2: Generate and Store a Token ---
                // Generate a unique token
                $token = bin2hex(random_bytes(16)); 
                
                // Set an expiration time (e.g., 1 hour from now)
                $expires = date("Y-m-d H:i:s", time() + 3600); // 3600 seconds = 1 hour
                
                // Insert the token, user ID, and expiration into the 'password_reset' table
                $sql_insert = "INSERT INTO password_reset (user_id, token, expires) 
                               VALUES (?, ?, ?) 
                               ON DUPLICATE KEY UPDATE token = ?, expires = ?";
                
                if ($stmt_insert = $conn->prepare($sql_insert)) {
                    // Bind parameters for insert and update
                    $stmt_insert->bind_param("issss", $user_id, $token, $expires, $token, $expires);
                    
                    if ($stmt_insert->execute()) {
                        
                        // --- Step 3: Send Email with Link ---
                        // IMPORTANT: Replace 'yourdomain.com' and use a proper email library (like PHPMailer)
                        $reset_link = "http://yourdomain.com/pages/new-password.php?selector=" . $user_id . "&token=" . $token;
                        
                        // ... Actual email sending code goes here ...

                        $message = "A password reset link has been sent to your email address.";
                        
                        // FOR TESTING: Remove this line in production!
                        $message .= "<br>TEST LINK: <a href='$reset_link'>Click Here to test the link</a>";

                    } else {
                        $message = "Error generating reset link. Please try again.";
                    }
                    $stmt_insert->close();
                }

            } else {
                // Security best practice: Do not confirm if the email exists.
                $message = "If an account with that email exists, a reset link has been sent.";
            }
            $stmt->close();
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../styles/user-login.css">
</head>
<body>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
        <section class="form-title">
            <h2>Reset Password</h2>
        </section>

        <?php if ($message): ?>
            <p style="color: red; padding: 10px; border: 1px solid red; border-radius: 5px;"><?php echo $message; ?></p>
        <?php endif; ?>

        <section>
            <input type="email" name="email" placeholder="Enter your email" required> 
        </section>
        <section>
            <input type="submit" name="submit" value="Send Reset Link" class="submit-btn">
        </section>
        <section class="links">
            <h6><a href="../pages/user-login.php">Back to Login</a></h6>
        </section>
    </form>

</body>
</html>