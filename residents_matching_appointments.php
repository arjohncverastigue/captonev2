<?php 
// Backend logic
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Residents') {
    header("Location: login.php");
    exit();
}

include 'conn.php';

$userId = $_SESSION['user_id'];

// Query to get the user's own appointment schedule
$queryOwnSchedule = "SELECT scheduled_for, status FROM appointments WHERE user_id = :user_id AND status IN ('Confirmed', 'Pending')";
$stmtOwn = $pdo->prepare($queryOwnSchedule);
$stmtOwn->execute(['user_id' => $userId]);
$ownSchedule = $stmtOwn->fetch(PDO::FETCH_ASSOC);

if ($ownSchedule && $ownSchedule['scheduled_for'] !== null) {
    $scheduleDate = $ownSchedule['scheduled_for'];

    // Query to fetch other appointments matching the same schedule
    $queryMatchingSchedules = "SELECT COUNT(*) AS count, scheduled_for
                                FROM appointments
                                WHERE scheduled_for = :scheduled_for 
                                AND user_id != :user_id
                                AND status IN ('Confirmed', 'Pending')
                                GROUP BY scheduled_for";
    $stmtMatching = $pdo->prepare($queryMatchingSchedules);
    $stmtMatching->execute([
        'scheduled_for' => $scheduleDate,
        'user_id' => $userId
    ]);
    $matchingAppointments = $stmtMatching->fetchAll(PDO::FETCH_ASSOC);
} else {
    $matchingAppointments = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Appointments Matching Your Schedule</h3>
        <?php if (!empty($ownSchedule) && $ownSchedule['scheduled_for'] !== null): ?>
            <p>Your Schedule: <strong><?php echo date('F d, Y h:i A', strtotime($ownSchedule['scheduled_for'])); ?></strong></p>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Scheduled For</th>
                        <th>Number of Appointments</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($matchingAppointments)): ?>
                        <?php foreach ($matchingAppointments as $index => $appointment): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo date('F d, Y h:i A', strtotime($appointment['scheduled_for'])); ?></td>
                                <td><?php echo $appointment['count']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No matching appointments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-warning">You have no scheduled appointments.</p>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
