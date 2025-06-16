<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Residents') {
    header("Location: login.php");
    exit();
}

include 'conn.php';
$userId = $_SESSION['user_id'];

// Handle AJAX feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    $appointmentId = $_POST['appointment_id'] ?? null;
    $feedback = trim($_POST['feedback'] ?? '');

    header('Content-Type: application/json');

    if ($appointmentId && $feedback) {
        $queryInsertFeedback = "INSERT INTO feedback (appointment_id, user_id, feedback) VALUES (:appointment_id, :user_id, :feedback)";
        $stmtInsert = $pdo->prepare($queryInsertFeedback);

        try {
            // Insert feedback
            $stmtInsert->execute([
                'appointment_id' => $appointmentId,
                'user_id' => $userId,
                'feedback' => $feedback
            ]);

            // Update appointment feedback_status to 'done'
            $queryUpdateStatus = "UPDATE appointments SET feedback_status = 'done' WHERE id = :appointment_id";
            $stmtUpdate = $pdo->prepare($queryUpdateStatus);
            $stmtUpdate->execute(['appointment_id' => $appointmentId]);

            echo json_encode(['success' => true, 'message' => 'Feedback submitted successfully.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Please select an appointment and provide your feedback.']);
    }
    exit();
}

// Get completed appointments that do NOT yet have feedback
$queryCompletedAppointments = "
    SELECT a.id AS appointment_id, d.name AS department_name, a.scheduled_for
    FROM appointments a
    JOIN departments d ON a.department_id = d.id
    WHERE a.user_id = :user_id 
      AND a.status = 'Completed'
      AND a.feedback_status = 'pending'
";
$stmt = $pdo->prepare($queryCompletedAppointments);
$stmt->execute(['user_id' => $userId]);
$completedAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="p-4">
<div class="container">
    <h3>Submit Feedback</h3>

    <div id="alert-container"></div>

    <?php if (!empty($completedAppointments)): ?>
        <form id="feedback-form">
            <div class="form-group">
                <label for="appointment_id">Select Completed Appointment:</label>
                <select class="form-control" id="appointment_id" name="appointment_id" required>
                    <option value="">-- Select Appointment --</option>
                    <?php foreach ($completedAppointments as $appointment): ?>
                        <option value="<?php echo $appointment['appointment_id']; ?>">
                            <?php echo $appointment['department_name'] . " - " . date('F d, Y h:i A', strtotime($appointment['scheduled_for'])); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="feedback">Your Feedback:</label>
                <textarea class="form-control" id="feedback" name="feedback" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Feedback</button>
        </form>
    <?php else: ?>
        <div class="alert alert-warning">You have no completed appointments to provide feedback for.</div>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function () {
        $('#feedback-form').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: 'residents_feedback.php',
                type: 'POST',
                data: {
                    ajax: true,
                    appointment_id: $('#appointment_id').val(),
                    feedback: $('#feedback').val()
                },
                success: function (response) {
                    if (response.success) {
                        alert(response.message);
                        $('#feedback-form')[0].reset();
                        location.reload(); // reload the page to refresh the dropdown
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert("An unexpected error occurred. Please try again.");
                }
            });
        });
    });
</script>
</body>
</html>
