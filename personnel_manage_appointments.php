<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'LGU Personnel') {
    echo "<script>alert('Unauthorized access!'); window.location.href='login.php';</script>";
    exit();
}

// Get the logged-in personnel's department_id
$stmt = $pdo->prepare("SELECT department_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$department_id = $stmt->fetchColumn();

// Get appointments for that department with 'Pending' status only
$appointments = $pdo->prepare("
    SELECT a.id, a.reason, a.status, a.requested_at, a.scheduled_for,
           u.first_name, u.last_name, auth.email
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    JOIN auth ON u.id = auth.user_id
    WHERE a.department_id = ?
      AND a.status = 'Pending'
    ORDER BY u.last_name ASC, a.scheduled_for ASC
");
$appointments->execute([$department_id]);
$appointmentData = $appointments->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="p-4">
<div class="container">
    <h3>Manage Appointments (Your Department Only)</h3>

    <div id="message-box"></div>

    <div id="appointments-container" class="mt-4">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>Residents</th>
                <th>Email</th>
                <th>Reason</th>
                <th>Scheduled For</th>
                <th>Status</th>
                <th>Requested At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($appointmentData)): ?>
                <?php $count = 1; foreach ($appointmentData as $app): ?>
                    <tr id="row-<?= $app['id'] ?>">
                        <td><?= $count++ ?></td>
                        <td><?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?></td>
                        <td><?= htmlspecialchars($app['email']) ?></td>
                        <td><?= htmlspecialchars($app['reason']) ?></td>
                        <td><?= $app['scheduled_for'] ?? 'N/A' ?></td>
                        <td class="status-text"><?= htmlspecialchars($app['status']) ?></td>
                        <td><?= htmlspecialchars($app['requested_at']) ?></td>
                        <td>
                            <button class="btn btn-success btn-sm complete-btn" data-id="<?= $app['id'] ?>" data-toggle="modal" data-target="#completeModal">Complete</button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $app['id'] ?>" data-toggle="modal" data-target="#deleteModal">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center text-muted">No appointments found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="complete-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Completion</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    Are you sure you want to mark this appointment as completed?
                    <input type="hidden" name="appointment_id" id="complete-appointment-id">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Yes, Complete</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="delete-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this appointment?
                    <input type="hidden" name="appointment_id" id="delete-appointment-id">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function () {
    $(document).on('click', '.complete-btn', function () {
        $('#complete-appointment-id').val($(this).data('id'));
    });

    $(document).on('click', '.delete-btn', function () {
        $('#delete-appointment-id').val($(this).data('id'));
    });

    $('#complete-form').submit(function (e) {
        e.preventDefault();
        const id = $('#complete-appointment-id').val();
        $.post('complete_appointment.php', { appointment_id: id }, function (res) {
            const r = JSON.parse(res);
            if (r.success) {
                $('#row-' + id).remove();
                $('#message-box').html('<div class="alert alert-success">' + r.message + '</div>');
                $('#completeModal').modal('hide');
            }
        });
    });

    $('#delete-form').submit(function (e) {
        e.preventDefault();
        const id = $('#delete-appointment-id').val();
        $.post('delete_appointment.php', { appointment_id: id }, function (res) {
            const r = JSON.parse(res);
            if (r.success) {
                $('#row-' + id).remove();
                $('#message-box').html('<div class="alert alert-success">' + r.message + '</div>');
                $('#deleteModal').modal('hide');
            }
        });
    });
});
</script>
</body>
</html>
