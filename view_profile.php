<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['user_id'])) {
  echo "<div class='alert alert-danger'>Unauthorized access.</div>";
  exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT u.*, a.email FROM users u JOIN auth a ON u.id = a.user_id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
  echo "<div class='alert alert-danger'>User not found.</div>";
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>
    .profile-card {
      max-width: 720px;
      margin: 40px auto;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      background: #fff;
    }
    .profile-icon {
      font-size: 60px;
      color: #0d6efd;
    }
    .profile-label {
      font-weight: 600;
      color: #555;
    }
    .profile-value {
      font-size: 16px;
      color: #222;
    }
    hr {
      border-top: 1px dashed #ccc;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="profile-card text-center">
      <i class='bx bx-user-circle profile-icon mb-3'></i>
      <h4 class="mb-4 text-primary">My Profile</h4>

      <div class="text-left">
        <div class="row mb-2">
          <div class="col-md-4 profile-label">First Name:</div>
          <div class="col-md-8 profile-value"><?= htmlspecialchars($user['first_name']) ?></div>
        </div>
        <div class="row mb-2">
          <div class="col-md-4 profile-label">Middle Name:</div>
          <div class="col-md-8 profile-value"><?= htmlspecialchars($user['middle_name']) ?></div>
        </div>
        <div class="row mb-2">
          <div class="col-md-4 profile-label">Last Name:</div>
          <div class="col-md-8 profile-value"><?= htmlspecialchars($user['last_name']) ?></div>
        </div>
        <hr>
        <div class="row mb-2">
          <div class="col-md-4 profile-label">Email:</div>
          <div class="col-md-8 profile-value"><?= htmlspecialchars($user['email']) ?></div>
        </div>
        <div class="row mb-2">
          <div class="col-md-4 profile-label">Address:</div>
          <div class="col-md-8 profile-value"><?= htmlspecialchars($user['address']) ?></div>
        </div>
        <div class="row mb-2">
          <div class="col-md-4 profile-label">Birthday:</div>
          <div class="col-md-8 profile-value"><?= htmlspecialchars($user['birthday']) ?></div>
        </div>
        <div class="row mb-2">
          <div class="col-md-4 profile-label">Age:</div>
          <div class="col-md-8 profile-value"><?= htmlspecialchars($user['age']) ?></div>
        </div>
        <div class="row mb-2">
          <div class="col-md-4 profile-label">Sex:</div>
          <div class="col-md-8 profile-value"><?= htmlspecialchars($user['sex']) ?></div>
        </div>
        <div class="row mb-2">
          <div class="col-md-4 profile-label">Civil Status:</div>
          <div class="col-md-8 profile-value"><?= htmlspecialchars($user['civil_status']) ?></div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
