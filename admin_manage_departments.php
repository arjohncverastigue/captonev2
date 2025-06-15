<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
include 'conn.php';

// Fetch departments
$stmt = $pdo->query("SELECT * FROM departments ORDER BY created_at DESC");
$departments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Departments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
    <h3>Manage Departments</h3>
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addModal">Add Department</button>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Services</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($departments as $d): ?>
                <tr data-id="<?= $d['id'] ?>">
                    <td><?= htmlspecialchars($d['name']) ?></td>
                    <td><?= htmlspecialchars($d['description']) ?></td>
                    <td><?= htmlspecialchars($d['services']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-info editBtn"
                            data-id="<?= $d['id'] ?>"
                            data-name="<?= htmlspecialchars($d['name']) ?>"
                            data-description="<?= htmlspecialchars($d['description']) ?>"
                            data-services="<?= htmlspecialchars($d['services']) ?>">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="<?= $d['id'] ?>">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="addForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Department</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input name="name" class="form-control mb-2" placeholder="Department Name" required>
                <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
                <input name="services" class="form-control mb-2" placeholder="Services Offered">
            </div>
            <div class="modal-footer">
                <button class="btn btn-success">Add</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm" class="modal-content">
            <input type="hidden" name="id">
            <div class="modal-header">
                <h5 class="modal-title">Edit Department</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input name="name" class="form-control mb-2" placeholder="Department Name" required>
                <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
                <input name="services" class="form-control mb-2" placeholder="Services Offered">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Add department
    $("#addForm").submit(function(e) {
        e.preventDefault();
        $.post("ajax_add_department.php", $(this).serialize(), function() {
            location.reload();
        }).fail(function(xhr) {
            alert("Error: " + xhr.responseText);
        });
    });

    // Populate edit modal
    $(".editBtn").click(function() {
        const row = $(this);
        $("#editForm [name=id]").val(row.data("id"));
        $("#editForm [name=name]").val(row.data("name"));
        $("#editForm [name=description]").val(row.data("description"));
        $("#editForm [name=services]").val(row.data("services"));
        $("#editModal").modal("show");
    });

    // Update department
    $("#editForm").submit(function(e) {
        e.preventDefault();
        $.post("ajax_edit_department.php", $(this).serialize(), function() {
            location.reload();
        }).fail(function(xhr) {
            alert("Error: " + xhr.responseText);
        });
    });

    // Delete department
    $(".deleteBtn").click(function() {
        if (confirm("Delete this department?")) {
            const id = $(this).data("id");
            $.post("ajax_delete_department.php", { id }, function() {
                location.reload();
            }).fail(function(xhr) {
                alert("Error: " + xhr.responseText);
            });
        }
    });
</script>
</body>
</html>
