<?php
// Include the database connection
// NOTE: Adjust the path if necessary
include("../config/database.php");

$message = "";
$valid_token = false;

// Get the selector (user ID) and token from the URL query string
$selector = filter_input(INPUT_GET, 'selector', FILTER_SANITIZE_NUMBER_INT);
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Check if selector and token are present in the URL
if ($selector && $token) {
    // --- Step 1: Validate the Token and Expiration ---
    $current_time = date("Y-m-d H:i:s");
    
    // Check the token against the database, ensuring it hasn't expired
    $sql_check = "SELECT user_id FROM password_reset WHERE user_id = ? AND token = ? AND expires >= ?";
    
    if ($stmt = $conn->prepare($sql_check)) {
        $stmt->bind_param("iss", $selector, $token, $current_time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $valid_token = true;
            // Token is valid, now check for a new password submission
            
            if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
                // Sanitize and validate the new password
                $new_password = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_SPECIAL_CHARS);
                $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_SPECIAL_CHARS);

                if (empty($new_password) || empty($confirm_password) || $new_password !== $confirm_password) {
                    $message = "Passwords do not match or are empty.";
                } else if (strlen($new_password) < 8) { // Basic length check
                    $message = "Password must be at least 8 characters long.";
                } else {
                    // --- Step 2: Hash and Update the Password ---
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $user_id_to_update = $selector; // The selector is the user_id

                    $sql_update = "UPDATE users SET password = ? WHERE id = ?";
                    
                    if ($stmt_update = $conn->prepare($sql_update)) {
                        $stmt_update->bind_param("si", $hashed_password, $user_id_to_update);
                        
                        if ($stmt_update->execute()) {
                            // --- Step 3: Delete the Token after successful reset ---
                            $sql_delete = "DELETE FROM password_reset WHERE user_id = ?";
                            $stmt_delete = $conn->prepare($sql_delete);
                            $stmt_delete->bind_param("i", $user_id_to_update);
                            $stmt_delete->execute();
                            $stmt_delete->close();
                            
                            $message = "✅ Your password has been successfully reset! You can now log in.";
                            
                        } else {
                            $message = "Error updating password. Please try again.";
                        }
                        $stmt_update->close();
                    }
                }
            }

        } else {
            $message = "❌ Invalid or expired reset token. Please request a new link.";
        }
        $stmt->close();
    }
    $conn->close();
} else {
    $message = "❌ Missing token or selector. Please use the complete link from your email.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password</title>
    <link rel="stylesheet" href="../styles/user-login.css">
</head>
<body>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?selector=" . $selector . "&token=" . $token)?>" method="post">
        <section class="form-title">
            <h2>Set New Password</h2>
        </section>

        <?php if ($message): ?>
            <p style="color: red; padding: 10px; border: 1px solid red; border-radius: 5px;"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if ($valid_token): // Only show the form if the token is valid ?>
            <section>
                <input type="password" name="new_password" placeholder="New Password" required> 
            </section>
            <section>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required> <br>
            </section>
            <section>
                <input type="submit" name="submit" value="Reset Password" class="submit-btn">
            </section>
        <?php endif; ?>
        <section class="links">
            <h6><a href="../pages/user-login.php">Go to Login</a></h6>
        </section>
    </form>

</body>
</html>