<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    exit("Unauthorized");
}
include 'conn.php';

$id = $_POST['id'];

$stmt = $pdo->prepare("DELETE FROM departments WHERE id = ?");
$stmt->execute([$id]);

echo "Department deleted";
