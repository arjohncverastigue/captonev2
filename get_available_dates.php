<?php
include 'conn.php';

$departmentId = $_GET['department_id'];

$stmt = $pdo->prepare("
    SELECT id, DATE(date_time) AS date 
    FROM available_dates 
    WHERE department_id = ? 
      AND DATE(date_time) >= CURDATE()
      AND MONTH(date_time) = MONTH(CURDATE())
      AND YEAR(date_time) = YEAR(CURDATE())
");
$stmt->execute([$departmentId]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
