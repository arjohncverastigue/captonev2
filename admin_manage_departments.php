<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
include 'conn.php';

// Fetch departments with services and requirements
$stmt = $pdo->query("SELECT d.*, 
                            GROUP_CONCAT(s.id ORDER BY s.id) AS service_ids,
                            GROUP_CONCAT(s.service_name ORDER BY s.id SEPARATOR '||') AS service_names
                     FROM departments d
                     LEFT JOIN department_services s ON d.id = s.department_id
                     GROUP BY d.id ORDER BY d.created_at DESC");
$departments = $stmt->fetchAll();

// Fetch requirements per service
$reqStmt = $pdo->query("SELECT * FROM service_requirements");
$requirements = $reqStmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE);
$reqMap = [];
foreach ($requirements as $req) {
    $reqMap[$req['service_id']][] = $req;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Departments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        .card { cursor: pointer; }
        .card .badge {
            font-size: 0.85em;
            padding: 0.4em 0.6em;
        }
        .modal-header { background-color: #5a5cb7; color: white; }
        .modal-body ul li::before { content: "✔️"; margin-right: 8px; color: #5a5cb7; }
    </style>
</head>
<body class="p-4">
<div class="container">
    <h3>Manage Departments</h3>
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addModal">Add Department</button>
    
    <div class="input-group mb-3">
    <input type="text" class="form-control" id="searchInput" placeholder="Search departments...">
    <div class="input-group-append">
        <button class="btn btn-outline-secondary" id="clearFilters">Clear Filters</button>
    </div>
</div>
    <div class="row">
        <?php foreach ($departments as $d): 
            $serviceIds = explode(',', $d['service_ids'] ?? '');
            $serviceNames = explode('||', $d['service_names'] ?? '');
        ?>
            <div class="col-md-4 mb-4 dept-card"
                data-search="<?= htmlspecialchars(strtolower($d['name'] . ' ' . $d['description'] . ' ' . implode(' ', $serviceNames))) ?>">
                <div class="card h-100" data-toggle="modal" data-target="#viewModal<?= $d['id'] ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($d['name']) ?></h5>
                        <?php foreach ($serviceNames as $svc): ?>
                            <span class="badge badge-info mr-1 mb-1"><?= htmlspecialchars($svc) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- View Modal -->
            <div class="modal fade" id="viewModal<?= $d['id'] ?>">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= htmlspecialchars($d['name']) ?></h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Description:</strong> <?= htmlspecialchars($d['description']) ?></p>
                            <p><strong>Services</strong></p>
                            <ul>
                                <?php foreach ($serviceIds as $i => $svcId): ?>
                                    <li>
                                        <strong><?= htmlspecialchars($serviceNames[$i]) ?></strong>
                                        <?php if (!empty($reqMap[$svcId])): ?>
                                            <p><strong>Requirements</strong></p>
                                            <ul>
                                                <?php foreach ($reqMap[$svcId] as $req): ?>
                                                    <li><?= htmlspecialchars($req['requirement']) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <div class="text-muted ml-2">No requirements listed.</div>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-dismiss="modal" data-toggle="modal" data-target="#editModal<?= $d['id'] ?>">Edit</button>
                            <button class="btn btn-danger delete-department" data-id="<?= $d['id'] ?>">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?= $d['id'] ?>">
                <div class="modal-dialog">
                    <form class="modal-content editForm" data-id="<?= $d['id'] ?>">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Department</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <input name="name" class="form-control mb-2" value="<?= htmlspecialchars($d['name']) ?>" required>
                            <textarea name="description" class="form-control mb-2"><?= htmlspecialchars($d['description']) ?></textarea>
                            <label>Services Offered:</label>
                            <div class="serviceFieldsEdit mb-2">
                                <?php foreach ($serviceIds as $i => $svcId): ?>
                                    <div class="service-group mb-3">
                                        <div class="input-group mb-1">
                                            <input type="text" name="services[]" class="form-control" value="<?= htmlspecialchars($serviceNames[$i]) ?>" required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-danger removeService">Remove</button>
                                            </div>
                                        </div>
                                        <div class="requirement-group">
                                            <?php foreach ($reqMap[$svcId] ?? [] as $req): ?>
                                                <div class="input-group mb-1">
                                                    <input type="text" name="requirements[]" class="form-control" value="<?= htmlspecialchars($req['requirement']) ?>">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-danger removeRequirement">Remove</button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-secondary addRequirement">+ Requirement</button>
                                        <hr>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary addServiceEdit">+ Add Another Service</button>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <form id="addForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Department</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input name="name" class="form-control mb-2" placeholder="Department Name" required>
                <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
                <label>Services and Requirements:</label>
                <div id="serviceFields">
                    <div class="service-group mb-3">
                        <div class="input-group mb-1">
                            <input type="text" name="services[]" class="form-control" placeholder="Service name" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger removeService">Remove</button>
                            </div>
                        </div>
                        <div class="requirement-group"></div>
                        <button type="button" class="btn btn-sm btn-secondary addRequirement">+ Requirement</button>
                        <hr>
                    </div>
                </div>
                <button type="button" id="addService" class="btn btn-sm btn-secondary">+ Add Service</button>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success">Add Department</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).on('click', '#addService, .addServiceEdit', function () {
    const serviceGroup = `
        <div class="service-group mb-3">
            <div class="input-group mb-1">
                <input type="text" name="services[]" class="form-control" placeholder="Service name" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger removeService">Remove</button>
                </div>
            </div>
            <div class="requirement-group"></div>
            <button type="button" class="btn btn-sm btn-secondary addRequirement">+ Requirement</button>
            <hr>
        </div>`;
    $(this).siblings('.serviceFieldsEdit, #serviceFields').append(serviceGroup);
});

$(document).on('click', '.addRequirement', function () {
    const reqField = `<div class="input-group mb-1">
        <input type="text" name="requirements[]" class="form-control" placeholder="Requirement">
        <div class="input-group-append">
            <button type="button" class="btn btn-danger removeRequirement">Remove</button>
        </div>
    </div>`;
    $(this).siblings('.requirement-group').append(reqField);
});

$(document).on('click', '.removeRequirement', function () {
    $(this).closest('.input-group').remove();
});

$(document).on('click', '.removeService', function () {
    $(this).closest('.service-group').remove();
});

$('#addForm').submit(function(e) {
    e.preventDefault();
    if (confirm("Add this department and services with requirements?")) {
        $.post('ajax_add_department_with_services.php', $(this).serialize(), function(response) {
            location.reload();
        }).fail(function(xhr) {
            alert("Error: " + xhr.responseText);
        });
    }
});

$('.editForm').submit(function(e) {
    e.preventDefault();
    if (confirm("Update this department and services?")) {
        const form = $(this);
        const deptId = form.data('id');
        const formData = form.serialize() + `&id=${deptId}`;
        $.post('ajax_edit_department_with_services.php', formData, function(response) {
            location.reload();
        }).fail(function(xhr) {
            alert("Error: " + xhr.responseText);
        });
    }
});

$(document).on('click', '.delete-department', function () {
    const deptId = $(this).data('id');
    if (confirm("Are you sure you want to delete this department and all related services?")) {
        $.post('ajax_delete_department.php', { id: deptId }, function(response) {
            alert("Department deleted successfully.");
            location.reload();
        }).fail(function(xhr) {
            alert("Error: " + xhr.responseText);
        });
    }
});

$('#searchInput').on('input', function () {
    const val = $(this).val().toLowerCase();
    $('.dept-card').each(function () {
        const searchable = $(this).data('search');
        $(this).toggle(searchable.includes(val));
    });
});

$('#clearFilters').click(function () {
    $('#searchInput').val('');
    $('.dept-card').show();
});

</script>
</body>
</html>
