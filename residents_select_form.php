<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Residents') {
    echo "<div class='alert alert-danger'>Unauthorized access</div>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="p-4"></body>
<div class="container">
    <h4>Select Feedback Form</h4>
    <div class="form-group">
        <label for="form_type">Please choose a form:</label>
        <select id="form_type" class="form-control">
            <option value="">-- Select --</option>
            <option value="residents_submit_feedback.php">Client Feedback Form</option>
            <option value="residents_submit_commendation.php">Commendation Form</option>
            <option value="residents_submit_complaint.php">Complaint Form</option>
        </select>
    </div>
    <button class="btn btn-primary" id="proceed_btn">Proceed</button>
</div>

<script>
    document.getElementById('proceed_btn').addEventListener('click', function () {
        const selectedForm = document.getElementById('form_type').value;
        if (selectedForm) {
            // This assumes you're still inside residents_dashboard.php
            $("#content-area").load(selectedForm);
        } else {
            alert('Please select a form.');
        }
    });
</script>
</body>
</html>
