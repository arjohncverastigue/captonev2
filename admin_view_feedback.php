<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

include 'conn.php';

// Fetch departments for filter
$departments = $pdo->query("SELECT id, name FROM departments")->fetchAll();

// Get filter
$filter = $_GET['department_id'] ?? '';
$sql = "SELECT f.*, d.name AS department_name, u.first_name, u.last_name
        FROM feedback f
        JOIN appointments a ON f.appointment_id = a.id
        JOIN departments d ON a.department_id = d.id
        JOIN users u ON f.user_id = u.id";
if ($filter) {
    $sql .= " WHERE d.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$filter]);
} else {
    $stmt = $pdo->query($sql);
}
$feedbacks = $stmt->fetchAll();
?>

<div class="container">
<<<<<<< Updated upstream
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

=======
    <div class="card shadow mb-3">
        <div class="card-header bg-info text-white">
            <i class='bx bx-message-square-detail'></i> Service Feedback
        </div>
        <div class="card-body">
            <form id="filterForm" class="form-inline mb-3">
                <label class="mr-2">Filter by Department:</label>
                <select name="department_id" id="department_id" class="form-control">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= $filter == $dept['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if (count($feedbacks) > 0): ?>
                <?php foreach ($feedbacks as $f): ?>
                    <div class="card mb-3 border-left-info">
                        <div class="card-body">
                            <h5><i class='bx bx-user-circle'></i> <?= htmlspecialchars($f['first_name'] . ' ' . $f['last_name']) ?></h5>
                            <p><strong>Department:</strong> <?= htmlspecialchars($f['department_name']) ?></p>
                            <p><strong>Attending Employee:</strong> <?= htmlspecialchars($f['attending_employee_name']) ?></p>
                            <p><strong>Feedback:</strong> <?= nl2br(htmlspecialchars($f['feedback'])) ?></p>
                            <?php if ($f['comments']): ?>
                                <p><strong>Additional Comments:</strong> <?= nl2br(htmlspecialchars($f['comments'])) ?></p>
                            <?php endif; ?>
                            <small class="text-muted"><i class='bx bx-calendar'></i> <?= $f['created_at'] ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class='bx bx-info-circle'></i> No feedback found for the selected department.
                </div>
            <?php endif; ?>
        </div>
    </div>
>>>>>>> Stashed changes
</div>

<script>
    document.getElementById('filterForm').addEventListener('change', function () {
        const selectedDeptId = document.getElementById('department_id').value;
        const url = 'admin_view_feedback.php?department_id=' + encodeURIComponent(selectedDeptId);
        loadContent(url);
    });
</script>
