<?php
include 'conn.php';

$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$services = $_POST['services'] ?? [];

if (!$id || !$name || empty($services)) {
    http_response_code(400);
    echo "ID, name, and at least one service are required.";
    exit();
}

try {
    $pdo->beginTransaction();

    // Update department
    $stmt = $pdo->prepare("UPDATE departments SET name = ?, description = ? WHERE id = ?");
    $stmt->execute([$name, $description, $id]);

    // Delete old services
    $pdo->prepare("DELETE FROM department_services WHERE department_id = ?")->execute([$id]);

    // Insert new services
    $svcStmt = $pdo->prepare("INSERT INTO department_services (department_id, service_name) VALUES (?, ?)");
    foreach ($services as $service) {
        if (trim($service) !== '') {
            $svcStmt->execute([$id, trim($service)]);
        }
    }

    $pdo->commit();
    echo "Updated";
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo "Database error: " . $e->getMessage();
}