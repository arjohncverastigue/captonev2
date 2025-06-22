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

// Get pending appointments with service name
$appointments = $pdo->prepare("SELECT a.*, u.first_name, u.middle_name, u.last_name, u.address, u.birthday, u.age, u.sex, u.civil_status, auth.email, ds.service_name FROM appointments a JOIN users u ON a.user_id = u.id JOIN auth ON u.id = auth.user_id LEFT JOIN department_services ds ON a.service_id = ds.id WHERE a.department_id = ? AND a.status = 'Pending' ORDER BY a.scheduled_for ASC");
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
    <style>
        .card:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.2); cursor: pointer; }
        .modal-full-img .modal-dialog { max-width: 600px; }
        .modal-full-img img { width: 100%; height: auto; }
    </style>
</head>
<body class="p-4">
<div class="container">
    <h3>Manage Appointments (Your Department Only)</h3>

    <div class="input-group mb-3">
        <input type="text" class="form-control" id="searchInput" placeholder="Search appointments...">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" id="clearFilters">Clear Filters</button>
        </div>
    </div>

    <div id="message-box"></div>

    <div class="row" id="appointments-container">
        <?php if (!empty($appointmentData)): ?>
            <?php foreach ($appointmentData as $app): ?>
                <div class="col-md-4 mb-4 appointment-card">
                    <div class="card" data-toggle="modal" data-target="#viewModal<?= $app['id'] ?>">
                        <div class="card-body">
                            <h5 class="card-title">Transaction #<?= $app['id'] ?></h5>
                            <p><strong>Resident:</strong> <?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?></p>
                            <p><strong>Service:</strong> <?= htmlspecialchars($app['service_name'] ?? 'N/A') ?></p>
                            <p><strong>Reason:</strong> <?= htmlspecialchars($app['reason']) ?></p>
                            <p><strong>Scheduled:</strong> <?= $app['scheduled_for'] ?? 'N/A' ?></p>
                        </div>
                    </div>
                </div>

                <!-- Appointment Detail Modal -->
                <div class="modal fade" id="viewModal<?= $app['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Appointment Details - Transaction #<?= $app['id'] ?></h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Full Name:</strong> <?= htmlspecialchars($app['first_name'] . ' ' . $app['middle_name'] . ' ' . $app['last_name']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($app['email']) ?></p>
                                <p><strong>Address:</strong> <?= htmlspecialchars($app['address']) ?></p>
                                <p><strong>Birthday:</strong> <?= htmlspecialchars($app['birthday']) ?></p>
                                <p><strong>Age:</strong> <?= htmlspecialchars($app['age']) ?></p>
                                <p><strong>Sex:</strong> <?= htmlspecialchars($app['sex']) ?></p>
                                <p><strong>Civil Status:</strong> <?= htmlspecialchars($app['civil_status']) ?></p>
                                <p><strong>Service:</strong> <?= htmlspecialchars($app['service_name'] ?? 'N/A') ?></p>
                                <p><strong>Reason:</strong> <?= htmlspecialchars($app['reason']) ?></p>
                                <p><strong>Valid ID:</strong><br>
                                    <img src="<?= (strpos($app['valid_id_path'], 'uploads/') === 0) ? htmlspecialchars($app['valid_id_path']) : 'uploads/' . htmlspecialchars($app['valid_id_path']) ?>" alt="Valid ID" class="img-fluid clickable-id" style="max-width: 300px;" data-toggle="modal" data-target="#fullImageModal" data-img-src="<?= (strpos($app['valid_id_path'], 'uploads/') === 0) ? htmlspecialchars($app['valid_id_path']) : 'uploads/' . htmlspecialchars($app['valid_id_path']) ?>">
                                </p>
                                <div class="mt-3">
                                    <button class="btn btn-success complete-btn" data-id="<?= $app['id'] ?>" data-dismiss="modal">Complete</button>
                                    <button class="btn btn-danger delete-btn" data-id="<?= $app['id'] ?>" data-dismiss="modal">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted">No appointments found.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Full Image Modal -->
<div class="modal fade modal-full-img" id="fullImageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-2">
                <img src="" alt="Full Image" id="fullImage" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $(document).on('click', '.complete-btn', function () {
        const id = $(this).data('id');
        $.post('complete_appointment.php', { appointment_id: id }, function (res) {
            const r = JSON.parse(res);
            if (r.success) location.reload();
        });
    });

    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        $.post('delete_appointment.php', { appointment_id: id }, function (res) {
            const r = JSON.parse(res);
            if (r.success) location.reload();
        });
    });

    $('#searchInput').on('input', function () {
        const val = $(this).val().toLowerCase();
        $('.appointment-card').each(function () {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(val));
        });
    });

    $('#clearFilters').click(function () {
        $('#searchInput').val('');
        $('.appointment-card').show();
    });

    // Full image modal
    $('.clickable-id').on('click', function () {
        const src = $(this).data('img-src');
        $('#fullImage').attr('src', src);
    });

    $('#fullImageModal').on('hidden.bs.modal', function () {
        $('body').addClass('modal-open');
    });
});
</script>
</body>
</html>
