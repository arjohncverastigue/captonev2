<?php
session_start();
include 'conn.php';

$userId = $_SESSION['user_id'];
$departmentId = $_POST['department_id'];
$availableDateId = $_POST['available_date_id'];
$reason = $_POST['reason'] ?? null;

// Get the selected date_time (no status filter)
$stmt = $pdo->prepare("SELECT date_time FROM available_dates WHERE id = ?");
$stmt->execute([$availableDateId]);
$dateRow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dateRow) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid date selected.']);
    exit;
}

$dateTime = $dateRow['date_time'];

// Get assigned LGU Personnel for the department
$stmt = $pdo->prepare("
    SELECT u.id 
    FROM users u
    JOIN auth a ON u.id = a.user_id
    WHERE u.department_id = :department_id AND a.role = 'LGU Personnel'
    LIMIT 1
");
$stmt->execute(['department_id' => $departmentId]);
$personnel = $stmt->fetch(PDO::FETCH_ASSOC);
$personnelId = $personnel ? $personnel['id'] : null;

// Insert appointment with personnel_id
$insert = $pdo->prepare("
    INSERT INTO appointments (user_id, department_id, personnel_id, scheduled_for, reason)
    VALUES (?, ?, ?, ?, ?)
");
$insert->execute([$userId, $departmentId, $personnelId, $dateTime, $reason]);

echo json_encode(['status' => 'success', 'message' => 'Appointment successfully booked!']);
?>
