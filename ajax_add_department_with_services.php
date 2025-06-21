<?php
include 'conn.php';

$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$services = $_POST['services'] ?? [];

if (!$name || empty($services)) {
    http_response_code(400);
    echo "Department name and at least one service are required.";
    exit();
}

try {
    $pdo->beginTransaction();

    // Insert department
    $stmt = $pdo->prepare("INSERT INTO departments (name, description) VALUES (?, ?)");
    $stmt->execute([$name, $description]);
    $deptId = $pdo->lastInsertId();

    // Insert services
    $svcStmt = $pdo->prepare("INSERT INTO department_services (department_id, service_name) VALUES (?, ?)");
    foreach ($services as $service) {
        if (trim($service) !== '') {
            $svcStmt->execute([$deptId, trim($service)]);
        }
    }

    $pdo->commit();
    echo "Success";
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo "Database error: " . $e->getMessage();
}