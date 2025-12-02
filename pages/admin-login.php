<?php
session_start();
include("../config/database.php");

$error_message = "";

$max_attempts = 5;
$lock_duration = 30; 
$attempts_after_lock = 2; 


if (!isset($_SESSION['admin_attempts'])) {
    $_SESSION['admin_attempts'] = $max_attempts;
}
if (!isset($_SESSION['admin_lock_time'])) {
    $_SESSION['admin_lock_time'] = 0;
}

// Check if admin is currently locked
$current_time = time();
if ($_SESSION['admin_lock_time'] > $current_time) {
    $remaining = $_SESSION['admin_lock_time'] - $current_time;
    $error_message = "Too many attempts! Try again in $remaining seconds.";
}

// --- NEW CODE ADDED HERE: Handles lock expiration and grants 2 attempts ---
if ($_SESSION['admin_lock_time'] !== 0 && $_SESSION['admin_lock_time'] <= $current_time) {
    // Lock expired. Reset attempts to 2 and clear the lock time.
    $_SESSION['admin_attempts'] = $attempts_after_lock;
    $_SESSION['admin_lock_time'] = 0;
}
// --- END OF NEW CODE ---


// Handle login submission
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if ($_SESSION['admin_lock_time'] > $current_time) {
        $error_message = "You are locked. Try again in " . ($_SESSION['admin_lock_time'] - $current_time) . " seconds.";
    } else {
        $admin = filter_input(INPUT_POST, "admin", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = $_POST["password"];

        if(empty($admin) || empty($password)){
            $error_message = "All fields are required!";
        } else {
            $sql = "SELECT * FROM admins WHERE admin = ?";
            $stmt = mysqli_prepare($conn, $sql);

            if($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $admin);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if(mysqli_num_rows($result) > 0 ) {
                    $row = mysqli_fetch_assoc($result);
                    $storedPassword = $row['password'];

                    if(password_verify($password, $storedPassword)){
                        // SUCCESS → reset attempts
                        $_SESSION['admin_attempts'] = $max_attempts;
                        $_SESSION['admin_lock_time'] = 0;

                        session_regenerate_id(true);
                        $_SESSION['is_admin_logged_in'] = true;
                        $_SESSION['admin_name'] = $row['admin'];

                        header("Location: ../pages/admin-dashboard.php");
                        exit();
                    } else {
                        // WRONG PASSWORD → decrease attempts
                        $_SESSION['admin_attempts']--;
                        if ($_SESSION['admin_attempts'] <= 0){
                            $_SESSION['admin_lock_time'] = time() + $lock_duration;
                            $error_message = "Too many attempts! Locked for 30 sec.";
                        } else {
                            $error_message = "Incorrect Password. Attempts left: " . $_SESSION['admin_attempts'];
                        }
                    }
                } else {
                    // ADMIN NOT FOUND → decrease attempts
                    $_SESSION['admin_attempts']--;
                    if ($_SESSION['admin_attempts'] <= 0){
                        $_SESSION['admin_lock_time'] = time() + $lock_duration;
                        $error_message = "Account not found. Locked for 30 sec.";
                    } else {
                        $error_message = "Account not found. Attempts left: " . $_SESSION['admin_attempts'];
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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="fonts.googleapis.com" rel="stylesheet">
  <link rel="stylesheet" href="cdnjs.cloudflare.com"> 
  <title>Admin Login</title>
  <link rel="stylesheet" href="../styles/ad-login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="icon" href="../images/icons/bookIcon.png" type="image/png">
</head>
<body>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
    <?php if(!empty($error_message)): ?>
    <p style="color: red; text-align: center; background: #ffcccc; padding: 10px; border-radius: 5px; width: 430px; margin: 10px;;">
        <?php echo $error_message; ?>
    </p>
    <?php endif; ?>
    <section class="form-container">
      <section class="form-title">
        <img src="../images/bookTitle.png" alt="Logo" type="image" class="LOGO" width="100%" height="20%">
        <div class="login-text">
          <h3>Login</h3>
        </div>
      </section>
      <section class="sign-ups">
        <section>
          <input type="text" name="admin" placeholder="username"> <br>
        </section>
        <section class="password-section">
          <div class="password-wrapper">
          <input type="password" name="password" id="password" placeholder="password">
           <i id="show-password" class="fa-regular fa-eye"></i>
          <br>
    </div>
          <span class="login-attempt">
            <u>
                <?php
                if (isset($_SESSION['admin_lock_time']) && $_SESSION['admin_lock_time'] > time()) {
                    echo "Locked for " . ($_SESSION['admin_lock_time'] - time()) . "s";
                } elseif (isset($_SESSION['admin_attempts'])) {
                    echo "Attempts left: " . $_SESSION['admin_attempts'];
                }
                ?>
            </u>
        </span>
   
        </section>
        <section>
          <input type="submit" name="login" value="Log In" class="login-button">  
        </section>  
      </section>
      <section class="form-footer">
        <span>Not an admin? <a href="../pages/public-homepage.php">Exit</a></span>
      </section>
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
<?php if (isset($_SESSION['admin_lock_time']) && $_SESSION['admin_lock_time'] > time()): ?>
let remaining = <?php echo $_SESSION['admin_lock_time'] - time(); ?>;
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
