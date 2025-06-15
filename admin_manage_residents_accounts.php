<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

include 'conn.php';

// Handle delete request
if (isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];

    // Delete from auth and users table
    $pdo->prepare("DELETE FROM auth WHERE user_id = ?")->execute([$deleteId]);
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$deleteId]);

    echo json_encode(['status' => 'success']);
    exit();
}

// Get all residents
$stmt = $pdo->prepare("SELECT u.id, u.first_name, u.middle_name, u.last_name, u.created_at, a.email
                       FROM users u
                       JOIN auth a ON u.id = a.user_id
                       WHERE a.role = 'Residents'
                       ORDER BY u.created_at DESC");
$stmt->execute();
$residents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Residents Accounts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="p-4">
<div class="container">
    <h3>Manage Resident Accounts</h3>
    <table class="table table-bordered mt-4">
    <thead>
        <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Date Created</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($residents as $resident): ?>
            <tr>
                <td><?= htmlspecialchars($resident['first_name'] . ' ' . $resident['middle_name'] . ' ' . $resident['last_name']) ?></td>
                <td><?= htmlspecialchars($resident['email']) ?></td>
                <td><?= date('F j, Y - g:i A', strtotime($resident['created_at'])) ?></td>
                <td><button class="btn btn-danger btn-sm" onclick="deleteResident(<?= $resident['id'] ?>)">Delete</button></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<script>
function deleteResident(id) {
    if (confirm('Are you sure you want to delete this resident account?')) {
        $.post('admin_manage_residents_accounts.php', { delete_id: id }, function(response) {
            if (response.status === 'success') {
                alert('Resident account deleted successfully.');
                location.reload();
            } else {
                alert('Failed to delete resident account.');
            }
        }, 'json');
    }
}
</script>
</body>
</html>
