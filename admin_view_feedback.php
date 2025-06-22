<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
include 'conn.php';

$departments = $pdo->query("SELECT id, name FROM departments ORDER BY name ASC")->fetchAll();

$recentFeedback = $pdo->query("
    SELECT f.feedback, f.created_at, u.first_name, u.last_name, d.name AS department_name
    FROM feedback f
    JOIN users u ON f.user_id = u.id
    JOIN appointments a ON f.appointment_id = a.id
    JOIN departments d ON a.department_id = d.id
    ORDER BY f.created_at DESC LIMIT 10
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Feedback - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="p-4">
<div class="container">
    <div class="container mt-4" id="feedbackContainer">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-2 mb-md-0"><i class="bx bx-message-dots"></i> Resident Feedback</h5>
            <div class="form-group mb-0">
                <label for="departmentSelect" class="text-white small mb-1">Filter by Department</label>
                <select id="departmentSelect" class="form-select form-select-sm">
                    <option value="">-- Show Recent Feedback (Top 10) --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Resident</th>
                            <th>Department</th>
                            <th>Feedback</th>
                            <th>Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentFeedback)): ?>
                            <?php foreach ($recentFeedback as $fb): ?>
                                <tr>
                                    <td><?= htmlspecialchars($fb['first_name'] . ' ' . $fb['last_name']) ?></td>
                                    <td><?= htmlspecialchars($fb['department_name']) ?></td>
                                    <td class="text-start"><?= nl2br(htmlspecialchars($fb['feedback'])) ?></td>
                                    <td><?= date('F j, Y • g:i A', strtotime($fb['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No recent feedback found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div>

</div>

<script>
$("#departmentSelect").change(function(){
    const deptId = $(this).val();
    $.get("ajax_get_feedback_by_department.php", { department_id: deptId }, function(html){
        $("#feedbackContainer").html(html);
    });
});
</script>
</body>
</html>
