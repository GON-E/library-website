<?php
include("../config/database.php");

$message = "";
$valid_token = false;

// Get user_id and token from URL
$selector = filter_input(INPUT_GET, 'selector', FILTER_SANITIZE_NUMBER_INT);
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($selector && $token) {
    $current_time = date("Y-m-d H:i:s");
    
    // Verify token
    $sql_check = "SELECT user_id FROM password_reset WHERE user_id = ? AND token = ? AND expires >= ?";
    
    if ($stmt = $conn->prepare($sql_check)) {
        $stmt->bind_param("iss", $selector, $token, $current_time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $valid_token = true;
            
            // Handle password reset form submission
            if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];

                if (empty($new_password) || empty($confirm_password)) {
                    $message = "❌ Please fill in all fields.";
                } else if ($new_password !== $confirm_password) {
                    $message = "❌ Passwords do not match.";
                } else if (strlen($new_password) < 8) {
                    $message = "❌ Password must be at least 8 characters long.";
                } else {
                    // Hash and update password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $user_id_to_update = $selector;

                    $sql_update = "UPDATE users SET password = ? WHERE userId = ?";
                    
                    if ($stmt_update = $conn->prepare($sql_update)) {
                        $stmt_update->bind_param("si", $hashed_password, $user_id_to_update);
                        
                        if ($stmt_update->execute()) {
                            // Delete the used token
                            $sql_delete = "DELETE FROM password_reset WHERE user_id = ?";
                            $stmt_delete = $conn->prepare($sql_delete);
                            $stmt_delete->bind_param("i", $user_id_to_update);
                            $stmt_delete->execute();
                            $stmt_delete->close();
                            
                            $message = "✅ Your password has been successfully reset! You can now log in.";
                            $valid_token = false; // Hide the form after success
                        } else {
                            $message = "❌ Error updating password. Please try again.";
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
    $message = "❌ Missing token. Please use the complete link from the reset page.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password</title>
    <link rel="stylesheet" href="../styles/user-login.css">
    <style>
        .message-box {
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            text-align: center;
        }
        .success {
            background: #d4edda;
            border: 1px solid #3f7f45;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #dc3545;
            color: #721c24;
        }
    </style>
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?selector=" . $selector . "&token=" . $token)?>" method="post">
        <section class="form-title">
            <h2>Set New Password</h2>
        </section>

        <?php if ($message): ?>
            <div class="message-box <?php echo (strpos($message, '✅') !== false) ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($valid_token): ?>
            <section>
                <input type="password" name="new_password" placeholder="New Password (min 8 characters)" required> 
            </section>
            <section>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            </section>
            <section>
                <input type="submit" value="Reset Password" class="submit-btn">
            </section>
        <?php endif; ?>
        
        <section class="links">
            <h6><a href="../pages/user-login.php">Go to Login</a></h6>
        </section>
    </form>
</body>
</html>