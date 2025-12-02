<?php
session_start();
include("../config/database.php");
include("../fetch/user-login-fetch.php"); // your login logic

$max_attempts = 5;
$lock_duration = 60; // 1 minute

// Initialize attempts and lock if not set
if (!isset($_SESSION['user_attempts'])) {
    $_SESSION['user_attempts'] = $max_attempts;
}
if (!isset($_SESSION['user_lock_time'])) {
    $_SESSION['user_lock_time'] = 0;
}

// Check if user is currently locked
$current_time = time();
if ($_SESSION['user_lock_time'] > $current_time) {
    $remaining = $_SESSION['user_lock_time'] - $current_time;
    $error_message = "Too many attempts! Try again in $remaining seconds.";
}

// Handle login submission
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_SESSION['user_lock_time'] > $current_time) {
        $error_message = "You are locked. Try again in " . ($_SESSION['user_lock_time'] - $current_time) . " seconds.";
    } else {
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $password = $_POST["password"];

        if(empty($email) || empty($password)){
            $error_message = "All fields are required!";
        } else {
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);

            if($stmt){
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if(mysqli_num_rows($result) > 0){
                    $row = mysqli_fetch_assoc($result);
                    $storedPassword = $row['password'];

                    if(password_verify($password, $storedPassword)){
                        // SUCCESS → reset attempts
                        $_SESSION['user_attempts'] = $max_attempts;
                        $_SESSION['user_lock_time'] = 0;

                        session_regenerate_id(true);
                        $_SESSION['is_user_logged_in'] = true;
                        $_SESSION['user_email'] = $row['email'];
                        $_SESSION['user_id'] = $row['id'];

                        header("Location: ../pages/user-dashboard.php");
                        exit();
                    } else {
                        // WRONG PASSWORD → decrease attempts
                        $_SESSION['user_attempts']--;
                        if ($_SESSION['user_attempts'] <= 0){
                            $_SESSION['user_lock_time'] = time() + $lock_duration;
                            $error_message = "Too many attempts! Locked for 1 minute.";
                        } else {
                            $error_message = "Incorrect Password. Attempts left: " . $_SESSION['user_attempts'];
                        }
                    }
                } else {
                    // EMAIL NOT FOUND → decrease attempts
                    $_SESSION['user_attempts']--;
                    if ($_SESSION['user_attempts'] <= 0){
                        $_SESSION['user_lock_time'] = time() + $lock_duration;
                        $error_message = "Account not found. Locked for 1 minute.";
                    } else {
                        $error_message = "Account not found. Attempts left: " . $_SESSION['user_attempts'];
                    }   
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}
if(isset($conn)) { mysqli_close($conn); }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="../styles/user-log-in.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>
<body>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
        
    <img src="../images/bookTitle.png" alt="Logo" type="image" class="LOGO" width="100%" height="auto">
    <section class="form-title">
            <h2>User Login</h2>
        </section>

        <?php if (!empty($error_message)): ?>
            <p style="color: red; text-align: center; margin-bottom: 15px; background: #ffebeb; padding: 10px; border-radius: 5px;">
                <?php echo $error_message; ?>
            </p>
        <?php endif; ?>
            
        
        <section>
            <input type="text" name="email" placeholder="email" required> 
        </section>
        <section class="password-section">
            <div class="password-wrapper">
            <input type="password" name="password" id="password" placeholder="password" required>
            <i id="show-password" class="fa-regular fa-eye"></i>
            </div>
        </section>
        <!-- Login attempts / lock display -->
<span class="login-attempt">
    <u>
        <?php
        if (isset($_SESSION['user_lock_time']) && $_SESSION['user_lock_time'] > time()) {
            echo "Locked for " . ($_SESSION['user_lock_time'] - time()) . "s";
        } elseif (isset($_SESSION['user_attempts'])) {
            echo "Attempts left: " . $_SESSION['user_attempts'];
        }
        ?>
    </u>
</span>
   <!--<section>
        <input 
            type="submit" 
            name="submit" 
            value="Log In" 
            class="submit-btn"
            <?php if(isset($_SESSION['user_lock_time']) && $_SESSION['user_lock_time'] > time()) echo "disabled"; ?>
        >
    </section> -->

        <section>
            <input type="submit" name="submit" value="Log In" class="submit-btn">
        </section>
        <section class="links">
            <h4><a href="../pages/reset-password.php">Forgot Password</a></h4>
            
            <h4>Don't have an account?<a href="../pages/user-signup.php">Sign Up</a></h4>
        </section>
    </form>
<script>
// Show/hide password toggle
const passwordField = document.getElementById("password");
const showPassword = document.getElementById("show-password");

showPassword.addEventListener("click", () => {
    if (passwordField.type === "password") {
        passwordField.type = "text";
        showPassword.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        passwordField.type = "password";
        showPassword.classList.replace("fa-eye-slash", "fa-eye");
    }
});

// Optional: live countdown timer for lock
<?php if (isset($_SESSION['user_lock_time']) && $_SESSION['user_lock_time'] > time()): ?>
let remaining = <?php echo $_SESSION['user_lock_time'] - time(); ?>;
const attemptSpan = document.querySelector(".login-attempt u");

const timer = setInterval(() => {
    remaining--;
    attemptSpan.textContent = "Locked for " + remaining + "s";

    if (remaining <= 0) {
        clearInterval(timer);
        location.reload(); // refresh page when lock ends
    }
}, 1000);
<?php endif; ?>
</script>

</body>
</html>