<?php
include('../config/database.php');
include('../config/admin-auth.php');

// Handle delete report
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_report'])) {
    $report_id = $_POST['report_id'];
    
    // Use prepared statement to delete the report
    $delete_sql = "DELETE FROM reports WHERE report_id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($delete_stmt, "i", $report_id);
    mysqli_stmt_execute($delete_stmt);
    mysqli_stmt_close($delete_stmt);
    
    // Redirect to prevent form resubmission
    header("Location: admin-report.php");
    exit();
}

// Handle update report status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $report_id = $_POST['report_id'];
    $status = $_POST['status'];
    
    // Use prepared statement to update the status
    $update_sql = "UPDATE reports SET status = ? WHERE report_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "si", $status, $report_id);
    mysqli_stmt_execute($update_stmt);
    mysqli_stmt_close($update_stmt);
    
    // Redirect to prevent form resubmission
    header("Location: admin-report.php");
    exit();
}

// Ensure `reports` table exists (create if missing) to avoid fatal errors
$create_table_sql = "
    CREATE TABLE IF NOT EXISTS reports (
        report_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        user_name VARCHAR(100) NOT NULL,
        issue_type VARCHAR(100) NOT NULL,
        description LONGTEXT NOT NULL,
        status VARCHAR(50) DEFAULT 'Pending',
        report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if (!mysqli_query($conn, $create_table_sql)) {
    // If creation fails (permissions/DB errors), show a friendly message and stop further queries
    $db_err = mysqli_error($conn);
    echo "\n<div style=\"padding:20px;background:#fee;border:1px solid #f99;margin:20px;\">";
    echo "<strong>Database error:</strong> Could not ensure 'reports' table exists.<br>";
    echo htmlspecialchars($db_err);
    echo "</div>\n";
    $result = false;
} else {
    // Fetch all reports
    $sql = "SELECT * FROM reports ORDER BY report_date DESC";
    $result = mysqli_query($conn, $sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reports - Admin Dashboard</title>
    <!-- Assuming the stylesheet is in ../styles/admin-report.css -->
    <link rel="stylesheet" href="../styles/admin-report.css">
    <link rel="icon" href="../images/icons/bookIcon.png" type="image/png">
</head>
<body>
    <header><?php include('admin-header.php'); ?></header>
    
    <div class="main-container">
        <!-- Assuming admin-side-nav.php is the sidebar -->
        <section><?php include('admin-side-nav.php'); ?></section>
        
        <section class="report-container">
            <div class="report-header">
                <h1>User Reports</h1>
                <!-- Check if $result is valid before counting rows -->
                <p class="report-count">Total Reports: <?php echo $result ? mysqli_num_rows($result) : 0; ?></p>
            </div>

            <?php if($result && mysqli_num_rows($result) > 0): ?>
                <div class="reports-table-wrapper">
                    <table class="reports-table">
                        <thead>
                            <tr>
                                <th>Report ID</th>
                                <th>User Name</th>
                                <th>Issue Type</th>
                                <th>Description</th>
                                <th>Report Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($report = mysqli_fetch_assoc($result)): ?>
                                <tr class="report-row">
                                    <td class="report-id"><?php echo htmlspecialchars($report['report_id']); ?></td>
                                    <!-- Display user_name. Assuming it exists in the 'reports' table -->
                                    <td class="user-name"><?php echo htmlspecialchars($report['user_name']); ?></td>
                                    <td class="issue-type">
                                        <!-- Dynamic class for issue badge styling -->
                                        <span class="issue-badge issue-<?php echo strtolower(str_replace(' ', '-', $report['issue_type'])); ?>">
                                            <?php echo htmlspecialchars($report['issue_type']); ?>
                                        </span>
                                    </td>
                                    <td class="description">
                                        <!-- Shortened description for table view, full text in title for tooltip -->
                                        <span class="description-text" title="<?php echo htmlspecialchars($report['description']); ?>">
                                            <?php echo htmlspecialchars(substr($report['description'], 0, 50)); ?>
                                            <?php if(strlen($report['description']) > 50): ?>...<?php endif; ?>
                                        </span>
                                    </td>
                                    <td class="report-date"><?php echo date('M d, Y H:i', strtotime($report['report_date'])); ?></td>
                                    <td class="status">
                                        <!-- Status Update Form -->
                                        <form method="post" class="status-form" onsubmit="return confirm('Are you sure you want to change the status of Report ID: <?php echo $report['report_id']; ?>?');">
                                            <input type="hidden" name="report_id" value="<?php echo $report['report_id']; ?>">
                                            <!-- Dynamic class for status select styling -->
                                            <select name="status" class="status-select status-<?php echo strtolower(str_replace(' ', '-', $report['status'])); ?>" onchange="this.form.submit();">
                                                <option value="Pending" <?php echo ($report['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="In Progress" <?php echo ($report['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="Resolved" <?php echo ($report['status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td class="actions">
                                        <!-- View Button calls JS function to open modal -->
                                        <button class="view-btn" onclick="viewReport(
                                            <?php echo $report['report_id']; ?>, 
                                            '<?php echo addslashes(htmlspecialchars($report['user_name'])); ?>', 
                                            '<?php echo addslashes(htmlspecialchars($report['issue_type'])); ?>', 
                                            '<?php echo addslashes(htmlspecialchars($report['description'])); ?>', 
                                            '<?php echo $report['report_date']; ?>'
                                        )">
                                            View
                                        </button>
                                        <!-- Delete Form -->
                                        <form method="post" class="delete-form" onsubmit="return confirm('Are you sure you want to DELETE Report ID: <?php echo $report['report_id']; ?>? This action cannot be undone.');">
                                            <input type="hidden" name="report_id" value="<?php echo $report['report_id']; ?>">
                                            <button type="submit" name="delete_report" class="delete-btn">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-reports">
                    <p>No reports yet. The system is clean!</p>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- View Report Modal Structure -->
    <div id="reportModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeReportModal()">&times;</span>
            <h2>Report Details</h2>
            <div class="modal-body">
                <div class="detail-row">
                    <strong>Report ID:</strong>
                    <span id="modalReportId"></span>
                </div>
                <div class="detail-row">
                    <strong>User Name:</strong>
                    <span id="modalUserName"></span>
                </div>
                <div class="detail-row">
                    <strong>Issue Type:</strong>
                    <span id="modalIssueType"></span>
                </div>
                <div class="detail-row">
                    <strong>Report Date:</strong>
                    <span id="modalReportDate"></span>
                </div>
                <div class="detail-row">
                    <strong>Description:</strong>
                    <!-- Use <pre> or white-space: pre-wrap for preserving formatting -->
                    <p id="modalDescription" class="description-full"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to populate and show the modal
        function viewReport(reportId, userName, issueType, description, reportDate) {
            document.getElementById('modalReportId').textContent = reportId;
            document.getElementById('modalUserName').textContent = userName;
            document.getElementById('modalIssueType').textContent = issueType;
            document.getElementById('modalDescription').textContent = description;
            
            // Format date for better readability in the modal
            const date = new Date(reportDate);
            document.getElementById('modalReportDate').textContent = date.toLocaleString();
            
            document.getElementById('reportModal').style.display = 'flex'; // Changed to flex for better centering
        }

        // Function to hide the modal
        function closeReportModal() {
            document.getElementById('reportModal').style.display = 'none';
        }

        // Close the modal when the user clicks anywhere outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('reportModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>

    <footer><?php include('footer.php'); ?></footer>
</body>
</html>