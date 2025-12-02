<?php 
  include('../config/database.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="../styles/userreport.css">
  <link rel="icon" href="../images/icons/bookIcon.png" type="image/png">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>REPORT</title>
</head>

<body>
  <div class="main-container">
      <?php include('user-nav.php');?>

     <div class="container">
    <h2>Report an Issue</h2>

    <p>Select a problem:</p>

    <form id="reportForm" method="POST" action="../fetch/report-submit-fetch.php">
      <div class="option">
        <input type="radio" name="issue" id="bug" value="Bug in the system" required>
        <label for="bug">Bug in the system</label>
      </div>

      <div class="option">
        <input type="radio" name="issue" id="buttons" value="Buttons not working">
        <label for="buttons">Buttons not working</label>
      </div>

      <div class="option">
        <input type="radio" name="issue" id="other" value="Other problem">
        <label for="other">Other problem</label>
        <label>(Describe the issue)</label>
      </div>

      <div class="textbox-area">
        <label></label>
        <textarea name="description" id="description" placeholder="Type the problem here..." required></textarea>
      </div>
    
      <button type="submit" class="submit-btn">Submit Report</button>

      <a class="back-btn" href="../pages/user-homepage.php">Home</a>
    </form>    
  </div>
  </div>

    <div id="successMessage" class="success-message" style="display: none;">
      ✓ Report submitted successfully!
    </div>
    <div id="errorMessage" class="error-message" style="display: none;">
      ✗ Error submitting report. Please try again.
    </div>

  <script>
    document.getElementById('reportForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const issueInput = document.querySelector('input[name="issue"]:checked');
      if (!issueInput) {
        document.getElementById('errorMessage').textContent = '✗ Please select an issue type.';
        document.getElementById('errorMessage').style.display = 'block';
        return;
      }

      const issue = issueInput.value;
      const description = document.getElementById('description').value;

      try {
        const response = await fetch('../fetch/report-submit-fetch.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `issue=${encodeURIComponent(issue)}&description=${encodeURIComponent(description)}`
        });

        const result = await response.json();

        if (result && result.status && result.status === 'success') {
          document.getElementById('successMessage').textContent = '✓ ' + (result.message || 'Report submitted successfully!');
          document.getElementById('successMessage').style.display = 'block';
          document.getElementById('errorMessage').style.display = 'none';
          document.getElementById('reportForm').reset();

          setTimeout(() => {
            document.getElementById('successMessage').style.display = 'none';
          }, 5000);
        } else {
          const msg = (result && result.message) ? result.message : 'Error submitting report. Please try again.';
          document.getElementById('errorMessage').textContent = '✗ ' + msg;
          document.getElementById('errorMessage').style.display = 'block';
          document.getElementById('successMessage').style.display = 'none';
        }
      } catch (error) {
        document.getElementById('errorMessage').textContent = '✗ Error submitting report. Check console.';
        document.getElementById('errorMessage').style.display = 'block';
        console.error('Error:', error);
      }
    });
  </script>

  <section class="footer-inc"><?php include('footer.php');?></section>
</body>
</html>