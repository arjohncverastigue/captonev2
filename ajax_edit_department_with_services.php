<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deptId = $_POST['id'];
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $services = $_POST['services'];

    // Update department
    $stmt = $pdo->prepare("UPDATE departments SET name = ?, description = ? WHERE id = ?");
    $stmt->execute([$name, $desc, $deptId]);

    // Delete old services
    $pdo->prepare("DELETE FROM department_services WHERE department_id = ?")->execute([$deptId]);

    // Insert new services
    $svcStmt = $pdo->prepare("INSERT INTO department_services (department_id, service_name) VALUES (?, ?)");
    foreach ($services as $svc) {
        if (trim($svc) !== "") {
            $svcStmt->execute([$deptId, $svc]);
        }
    }

    echo "success";
} else {
    http_response_code(400);
    echo "Invalid request";
}
?>
