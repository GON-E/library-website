<?php
 include ('..config/database.php');
?>
<!--HTML-->
<!DOCTYPE html>
<html lang="en">
    <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>SETTINGS</title>
     <link rel="stylesheet" href="settings.css">
    </head>

<body>
    <div class = "container">
        <h2>Settings</h2>
        <div class = "settings-item">
            <label>Username</label>
            <input type="text" placeholder="Enter Username">
        </div>

        <div class="settings-item">
          <label>Theme</label>
          <select>
            <option>Light</option>
            <option>Dark</option>
          </select>
        </div>

    </div>
</body>
