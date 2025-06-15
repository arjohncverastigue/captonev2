<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

include 'conn.php';

header('Content-Type: application/json');

if (isset($_GET['department_id']) && $_GET['department_id'] !== '') {
    $deptId = $_GET['department_id'];

    $stmt = $pdo->prepare("
        SELECT a.status, a.reason, a.scheduled_for, a.requested_at,
               u.first_name, u.last_name,
               d.name AS department_name
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN departments d ON a.department_id = d.id
        WHERE d.id = :deptId
        ORDER BY u.last_name ASC, a.scheduled_for ASC
    ");
    $stmt->execute(['deptId' => $deptId]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} else {
    // Return 10 most recent appointments if no department selected
    $stmt = $pdo->query("
        SELECT a.status, a.reason, a.scheduled_for, a.requested_at,
               u.first_name, u.last_name,
               d.name AS department_name
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN departments d ON a.department_id = d.id
        ORDER BY a.requested_at DESC
        LIMIT 10
    ");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
