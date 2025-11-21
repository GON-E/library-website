<?php
 include("../config/database.php");
?>

<!--HTML STRUCTURE-->
<!DOCTYPE html>
<html lang="en">
<div class="sidebar">
    <div class="icon home"></div>
    <div class="icon menu"></div>
    <div class="icon settings"></div>
    <div class="icon flag"></div>
    <div class="icon info"></div>
    <div class="icon user"></div>
</div>

<div class="main-container">
    <header>
        <h2>Hello, User! Welcome to Lé Bros Library!</h2>
        <p>November 00, 2025 | Monday, 00:00 AM</p>
    </header>


    <!-- Top Info Boxes-->
    <div class="info-boxes">
        <div class="info-card">
            <h3>Books Available: <span>📘</span></h3>
            <p class="count">00</p>
        </div>

        <div class="info-card">
            <h3>Borrowed Books: <span>📚</span></h3>
            <p class="count">00</p>
        </div>

        <div class="info-card">
            <h3>Overdue Books: <span>⏳</span></h3>
            <p class="count">00</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-sections">

        <!-- Recently Borrowed Books Table -->
        <div class="borrowed-section">
            <h3>Recently Borrowed Books:</h3>
            <table class="borrowed-table">
                <thead>
                    <tr>
                        <th>Name of Book</th>
                        <th>Book ID</th>
                        <th>Book Type</th>
                        <th>Date Borrowed</th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- Recommended Books Section-->
        <div class="recommended-section">
            <h3>Recommended:</h3>

        </div>

    </div>
</div>



