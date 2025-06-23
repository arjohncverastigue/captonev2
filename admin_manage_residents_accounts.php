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
    $pdo->prepare("DELETE FROM auth WHERE user_id = ?")->execute([$deleteId]);
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$deleteId]);
    echo json_encode(['status' => 'success']);
    exit();
}

// Get all residents with full info
$stmt = $pdo->prepare("SELECT u.*, a.email
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
    <title>Manage Resident Accounts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .card:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.2); cursor: pointer; }
        .modal-full-img .modal-dialog { max-width: 600px; }
        .modal-full-img img { width: 100%; height: auto; }
    </style>
</head>
<body class="p-4">
<<<<<<< Updated upstream
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class='bx bx-group'></i> Manage Resident Accounts</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Date Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($residents)): ?>
                            <?php foreach ($residents as $resident): ?>
                                <tr>
                                    <td><?= htmlspecialchars($resident['first_name'] . ' ' . $resident['middle_name'] . ' ' . $resident['last_name']) ?></td>
                                    <td><?= htmlspecialchars($resident['email']) ?></td>
                                    <td><?= date('F j, Y - g:i A', strtotime($resident['created_at'])) ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteResident(<?= $resident['id'] ?>)">
                                            <i class='bx bx-trash'></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted">No resident accounts found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
=======
<div class="container">
    <h3 class="mb-4">Manage Resident Accounts</h3>
    <div class="row">
        <?php foreach ($residents as $resident): ?>
            <?php
                $validIdPath = htmlspecialchars($resident['valid_id_image']);
                $selfiePath = htmlspecialchars($resident['selfie_image']);

            ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm" data-toggle="modal" data-target="#residentModal<?= $resident['id'] ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($resident['first_name'] . ' ' . $resident['middle_name'] . ' ' . $resident['last_name']) ?></h5>
                        <p class="card-text">
                            Age: <?= htmlspecialchars($resident['age']) ?><br>
                            Address: <?= htmlspecialchars($resident['address']) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal for each resident -->
            <div class="modal fade" id="residentModal<?= $resident['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel<?= $resident['id'] ?>" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel<?= $resident['id'] ?>">Resident Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <p><strong>Full Name:</strong> <?= htmlspecialchars($resident['first_name'] . ' ' . $resident['middle_name'] . ' ' . $resident['last_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($resident['email']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($resident['address']) ?></p>
                    <p><strong>Birthday:</strong> <?= htmlspecialchars($resident['birthday']) ?></p>
                    <p><strong>Age:</strong> <?= htmlspecialchars($resident['age']) ?></p>
                    <p><strong>Sex:</strong> <?= htmlspecialchars($resident['sex']) ?></p>
                    <p><strong>Civil Status:</strong> <?= htmlspecialchars($resident['civil_status']) ?></p>
                    <p><strong>Valid ID Type:</strong> <?= htmlspecialchars($resident['valid_id_type']) ?></p>

                    <p><strong>Valid ID:</strong><br>
                        <img src="<?= $validIdPath ?>"
                             alt="Valid ID"
                             class="img-fluid clickable-image"
                             style="max-width:300px;"
                             data-toggle="modal"
                             data-target="#imageModal"
                             data-img-src="<?= $validIdPath ?>">
                    </p>

                    <p><strong>Selfie with ID:</strong><br>
                        <img src="<?= $selfiePath ?>"
                             alt="Selfie"
                             class="img-fluid clickable-image"
                             style="max-width:300px;"
                             data-toggle="modal"
                             data-target="#imageModal"
                             data-img-src="<?= $selfiePath ?>">
                    </p>
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-danger" onclick="deleteResident(<?= $resident['id'] ?>)">Delete Account</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Full Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-full-img" role="document">
        <div class="modal-content">
            <div class="modal-body p-2 text-center">
                <img src="" alt="Full Image" id="modalFullImage" class="img-fluid">
>>>>>>> Stashed changes
            </div>
        </div>
    </div>
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

// Image modal preview
$(document).on('click', '.clickable-image', function () {
    const src = $(this).data('img-src');
    $('#modalFullImage').attr('src', src);
});
</script>
</body>
</html>
