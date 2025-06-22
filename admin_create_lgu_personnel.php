<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
include 'conn.php';

// Fetch departments
$departments = $pdo->query("SELECT id, name FROM departments ORDER BY name ASC")->fetchAll();

// Fetch LGU Personnel (joined with auth to get email)
$stmt = $pdo->prepare("
    SELECT u.id, u.first_name, u.middle_name, u.last_name, u.department_id, a.email, d.name AS dept_name 
    FROM users u 
    JOIN auth a ON u.id = a.user_id 
    LEFT JOIN departments d ON u.department_id = d.id 
    WHERE a.role = 'LGU Personnel'
");
$stmt->execute();
$personnel = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage LGU Personnel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
<div class="container">
    <h3>Manage LGU Personnel</h3>
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addModal">Add LGU Personnel</button>
    <div id="responseMsg"></div>

    <!-- Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($personnel as $p): ?>
                <tr data-id="<?= $p['id'] ?>">
                    <td><?= htmlspecialchars("{$p['first_name']} {$p['middle_name']} {$p['last_name']}") ?></td>
                    <td><?= htmlspecialchars($p['email']) ?></td>
                    <td><?= htmlspecialchars($p['dept_name']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-info editBtn" 
                            data-id="<?= $p['id'] ?>"
                            data-first="<?= $p['first_name'] ?>"
                            data-middle="<?= $p['middle_name'] ?>"
                            data-last="<?= $p['last_name'] ?>"
                            data-email="<?= $p['email'] ?>"
                            data-dept="<?= $p['department_id'] ?>">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="<?= $p['id'] ?>">Delete</button>
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
                <h5 class="modal-title">Add LGU Personnel</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input name="first_name" class="form-control mb-2" placeholder="First Name" required>
                <input name="middle_name" class="form-control mb-2" placeholder="Middle Name">
                <input name="last_name" class="form-control mb-2" placeholder="Last Name" required>

                <select name="department_id" class="form-control mb-2" required>
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <input name="email" type="email" class="form-control mb-2" placeholder="Email" required>
                <input name="password" type="password" class="form-control mb-2" placeholder="Password" required>
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
                <h5 class="modal-title">Edit LGU Personnel</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input name="first_name" class="form-control mb-2" placeholder="First Name" required>
                <input name="middle_name" class="form-control mb-2" placeholder="Middle Name">
                <input name="last_name" class="form-control mb-2" placeholder="Last Name" required>

                <select name="department_id" class="form-control mb-2" required>
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <input name="email" type="email" class="form-control mb-2" placeholder="Email" required>
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
    // ADD
    // ADD with confirmation
$("#addForm").submit(function(e) {
    e.preventDefault();
    if (confirm("Are you sure you want to add this personnel?")) {
        $.post("ajax_create_personnel.php", $(this).serialize(), function(res) {
            location.reload();
        }).fail(function(xhr) {
            alert("Error: " + xhr.responseText);
        });
    }
});

    // FILL EDIT MODAL
    $(".editBtn").click(function() {
        const btn = $(this);
        $("#editForm [name=id]").val(btn.data("id"));
        $("#editForm [name=first_name]").val(btn.data("first"));
        $("#editForm [name=middle_name]").val(btn.data("middle"));
        $("#editForm [name=last_name]").val(btn.data("last"));
        $("#editForm [name=email]").val(btn.data("email"));
        $("#editForm [name=department_id]").val(btn.data("dept"));
        $("#editModal").modal("show");
    });

    // EDIT
    // EDIT with confirmation
$("#editForm").submit(function(e) {
    e.preventDefault();
    if (confirm("Are you sure you want to update this personnel's details?")) {
        $.post("ajax_update_personnel.php", $(this).serialize(), function(res) {
            location.reload();
        }).fail(function(xhr) {
            alert("Error: " + xhr.responseText);
        });
    }
});

    // DELETE
    $(".deleteBtn").click(function() {
        if (confirm("Are you sure you want to delete this personnel?")) {
            const id = $(this).data("id");
            $.post("ajax_delete_personnel.php", { id }, function(res) {
                location.reload();
            }).fail(function(xhr) {
                alert("Error: " + xhr.responseText);
            });
        }
    });
</script>
</body>
</html>
