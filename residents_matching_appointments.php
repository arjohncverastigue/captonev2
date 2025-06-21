<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Residents') {
    header("Location: login.php");
    exit();
}

include 'conn.php';
$userId = $_SESSION['user_id'];

$queryAppointments = "SELECT a.id, a.scheduled_for, a.status, d.name AS department_name, s.service_name
                      FROM appointments a
                      JOIN departments d ON a.department_id = d.id
                      JOIN department_services s ON a.service_id = s.id
                      WHERE a.user_id = :user_id AND a.status = 'Pending'
                      ORDER BY a.scheduled_for ASC";
$stmt = $pdo->prepare($queryAppointments);
$stmt->execute(['user_id' => $userId]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Pending Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h3>My Pending Appointments</h3>

    <?php if (empty($appointments)): ?>
        <div class="alert alert-info">You have no pending appointments.</div>
    <?php else: ?>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Transaction No.</th>
                    <th>Department</th>
                    <th>Service</th>
                    <th>Schedule</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $index => $appt): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($appt['id']) ?></td>
                        <td><?= htmlspecialchars($appt['department_name']) ?></td>
                        <td><?= htmlspecialchars($appt['service_name']) ?></td>
                        <td><?= date('F d, Y h:i A', strtotime($appt['scheduled_for'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" data-toggle="modal"
                                data-target="#matchModal<?= $appt['id'] ?>">View Matches</button>
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="matchModal<?= $appt['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Matching Appointments</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Your Schedule:</strong><br>
                                        <?= date('F d, Y h:i A', strtotime($appt['scheduled_for'])) ?>
                                    </p>
                                    <hr>
                                    <?php
                                    $matchQuery = "SELECT COUNT(*) AS total
                                                   FROM appointments
                                                   WHERE scheduled_for = :scheduled_for 
                                                   AND user_id != :user_id
                                                   AND status IN ('Pending', 'Confirmed')";
                                    $matchStmt = $pdo->prepare($matchQuery);
                                    $matchStmt->execute([
                                        'scheduled_for' => $appt['scheduled_for'],
                                        'user_id' => $userId
                                    ]);
                                    $matchResult = $matchStmt->fetch();
                                    ?>
                                    <p><strong>Matching Appointments:</strong> <?= $matchResult['total'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
