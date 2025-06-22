<?php
session_start();
include 'conn.php';
header('Content-Type: application/json');

$data = $_POST;
try {
  $stmt = $pdo->prepare("
    UPDATE users SET first_name=?, middle_name=?, last_name=?, address=?, birthday=?, age=?, sex=?, civil_status=?
    WHERE id=?
  ");
  $stmt->execute([
    $data['first_name'], $data['middle_name'], $data['last_name'],
    $data['address'], $data['birthday'], $data['age'],
    $data['sex'], $data['civil_status'], $data['user_id']
  ]);

  $stmt = $pdo->prepare("UPDATE auth SET email=? WHERE user_id=?");
  $stmt->execute([$data['email'], $data['user_id']]);

  echo json_encode(['status'=>'success','message'=>'Profile updated successfully.']);
} catch (Exception $e) {
  echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
