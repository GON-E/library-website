<?php 
include("../config/database.php");

$message = "";
$reset_link = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    
    if(!$email) {
        $message = "Please enter your email address.";
    } else {
        // Check if user exists
        $sql_check = "SELECT userId FROM users WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql_check)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user_row = $result->fetch_assoc();
                $user_id = $user_row['userId'];
                
                // Generate simple token
                $token = bin2hex(random_bytes(16)); 
                
                // Set expiration (1 hour)
                $expires = date("Y-m-d H:i:s", time() + 3600);
                
                // Delete old tokens for this user
                $sql_delete = "DELETE FROM password_reset WHERE user_id = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param("i", $user_id);
                $stmt_delete->execute();
                $stmt_delete->close();
                
                // Insert new token
                $sql_insert = "INSERT INTO password_reset (user_id, token, expires) VALUES (?, ?, ?)";
                
                if ($stmt_insert = $conn->prepare($sql_insert)) {
                    $stmt_insert->bind_param("iss", $user_id, $token, $expires);
                    
                    if ($stmt_insert->execute()) {
                        // Create the reset link using current server settings
                        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                        $host = $_SERVER['HTTP_HOST'];
                        $uri = $_SERVER['REQUEST_URI'];
                        $base_path = str_replace('reset-password.php', '', $uri);
                        
                        $reset_link = $protocol . "://" . $host . $base_path . "new-password.php?selector=" . $user_id . "&token=" . $token;
                        
                        $message = "✅ Reset link generated! Copy the link below and paste it in your browser.";
                    } else {
                        $message = "Error generating reset link. Please try again.";
                    }
                    $stmt_insert->close();
                }
            } else {
                // Don't reveal if email exists (security practice)
                $message = "If an account exists with that email, a reset link will be shown below.";
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
    <style>
        .reset-link-box {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            word-wrap: break-word;
        }
        .reset-link-box a {
            color: #3f7f45;
            text-decoration: none;
            font-weight: bold;
        }
        .copy-btn {
            background: #3f7f45;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .copy-btn:hover {
            background: #2e5e32;
        }
    </style>
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
        <section class="form-title">
            <h2>Reset Password</h2>
        </section>

        <?php if ($message): ?>
            <p style="padding: 10px; border: 1px solid #3f7f45; border-radius: 5px; background: #f0f0f0;">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <?php if ($reset_link): ?>
            <div class="reset-link-box">
                <p><strong>Your Reset Link:</strong></p>
                <a href="<?php echo $reset_link; ?>" target="_blank" id="resetLink">
                    <?php echo $reset_link; ?>
                </a>
                <br>
                <button type="button" class="copy-btn" onclick="copyLink()">Copy Link</button>
            </div>
            <p style="font-size: 12px; color: #666;">
                ⚠️ This link will expire in 1 hour. Click it or copy-paste it into your browser.
            </p>
        <?php endif; ?>

        <section>
            <input type="email" name="email" placeholder="Enter your email" required> 
        </section>
        <section>
            <input type="submit" value="Generate Reset Link" class="submit-btn">
        </section>
        <section class="links">
            <h6><a href="../pages/user-login.php">Back to Login</a></h6>
        </section>
    </form>

    <script>
        function copyLink() {
            const link = document.getElementById('resetLink').href;
            navigator.clipboard.writeText(link).then(function() {
                alert('Link copied to clipboard!');
            }, function() {
                alert('Failed to copy link. Please copy it manually.');
            });
        }
    </script>
</body>
</html>