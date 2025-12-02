<?php 
// config/user-auth.php

// Start the Session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Recognize several possible logged-in indicators and normalize keys
$loggedIn = false;
if (!empty($_SESSION['is_user_logged_in'])) $loggedIn = true;
if (!empty($_SESSION['userId']) || !empty($_SESSION['user_id'])) $loggedIn = true;

// Normalize id keys so other pages can rely on either form
if (!empty($_SESSION['userId']) && empty($_SESSION['user_id'])) {
    $_SESSION['user_id'] = $_SESSION['userId'];
}
if (!empty($_SESSION['user_id']) && empty($_SESSION['userId'])) {
    $_SESSION['userId'] = $_SESSION['user_id'];
}

// Normalize name keys if present
if (!empty($_SESSION['userName']) && empty($_SESSION['user_name'])) {
    $_SESSION['user_name'] = $_SESSION['userName'];
}
if (!empty($_SESSION['user_name']) && empty($_SESSION['userName'])) {
    $_SESSION['userName'] = $_SESSION['user_name'];
}

if (!$loggedIn) {
    header("Location: ../pages/user-login.php");
    exit();
}
?>