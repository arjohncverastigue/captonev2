<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
include 'conn.php';

// Fetch departments with services
$stmt = $pdo->query("SELECT d.*, GROUP_CONCAT(s.service_name SEPARATOR ', ') AS services
                     FROM departments d
                     LEFT JOIN department_services s ON d.id = s.department_id
                     GROUP BY d.id ORDER BY d.created_at DESC");
$departments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Departments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        .card .badge {
            font-size: 0.85em;
            padding: 0.4em 0.6em;
            cursor: pointer;
        }
        <style>
        .modal-header {
            background-color: #5a5cb7;
            color: white;
            border-top-left-radius: 0.3rem;
            border-top-right-radius: 0.3rem;
        }

        .modal-title {
            font-weight: bold;
            font-size: 1.25rem;
        }

        .modal-body {
            background-color: #f8f9fa;
            padding: 20px;
            border-bottom-left-radius: 0.3rem;
            border-bottom-right-radius: 0.3rem;
        }

        .modal-body p strong {
            color: #343a40;
        }

        .modal-body ul {
            list-style-type: none;
            padding-left: 0;
        }

        .modal-body ul li::before {
            content: "✔️";
            margin-right: 8px;
            color: #5a5cb7;
        }

        .close {
            color: white;
            opacity: 0.8;
        }

        .close:hover {
            color: #fff;
            opacity: 1;
        }
</style>
    </style>
</head>
<body class="p-4">
<div class="container">
    <h3>Manage Departments</h3>
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addModal">Add Department</button>

    <div class="input-group mb-3">
        <input type="text" class="form-control" id="searchInput" placeholder="Search department...">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" id="clearFilters">Clear Filters</button>
        </div>
    </div>

    <!-- Inside your <div class="row"> -->
<div class="row">
    <?php foreach ($departments as $d): 
        $searchText = strtolower($d['name'] . ' ' . $d['description'] . ' ' . $d['services']);
    ?>
        <div class="col-md-4 mb-4 dept-card" data-search="<?= htmlspecialchars($searchText) ?>" data-services="<?= strtolower($d['services']) ?>">
            <div class="card h-100" data-toggle="modal" data-target="#viewModal<?= $d['id'] ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($d['name']) ?></h5>
                    <?php
                    $services = array_filter(array_map('trim', explode(',', $d['services'])));
                    foreach ($services as $svc): ?>
                        <span class="badge badge-info mr-1 mb-1 service-badge" data-service="<?= strtolower($svc) ?>">
                            <?= htmlspecialchars($svc) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

            <!-- View Modal -->
            <div class="modal fade" id="viewModal<?= $d['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= htmlspecialchars($d['name']) ?></h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Description:</strong> <?= htmlspecialchars($d['description']) ?></p>
                            <p><strong>Services:</strong></p>
                            <ul>
                                <?php foreach ($services as $svc): ?>
                                    <li><?= htmlspecialchars($svc) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?= $d['id'] ?>" tabindex="-1">
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
                                <?php foreach ($services as $svc): ?>
                                    <div class="input-group mb-2">
                                        <input type="text" name="services[]" class="form-control" value="<?= htmlspecialchars($svc) ?>" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger remove-service">X</button>
                                        </div>
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
                <label>Services Offered:</label>
                <div id="serviceFields" class="mb-2">
                    <div class="input-group mb-2">
                        <input type="text" name="services[]" class="form-control" placeholder="Enter a service" required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-danger remove-service">X</button>
                        </div>
                    </div>
                </div>
                <button type="button" id="addService" class="btn btn-sm btn-secondary mb-2">+ Add Another Service</button>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success">Add</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let selectedServices = [];

// Add service field (Add Modal)
$("#addService").click(function () {
    $("#serviceFields").append(`
        <div class="input-group mb-2">
            <input type="text" name="services[]" class="form-control" placeholder="Enter a service" required>
            <div class="input-group-append">
                <button type="button" class="btn btn-danger remove-service">X</button>
            </div>
        </div>
    `);
});

// Add service field in edit modal
$(document).on("click", ".addServiceEdit", function () {
    $(this).siblings(".serviceFieldsEdit").append(`
        <div class="input-group mb-2">
            <input type="text" name="services[]" class="form-control" placeholder="Enter a service" required>
            <div class="input-group-append">
                <button type="button" class="btn btn-danger remove-service">X</button>
            </div>
        </div>
    `);
});

// Remove service field
$(document).on("click", ".remove-service", function () {
    $(this).closest(".input-group").remove();
});

// Submit add form
$("#addForm").submit(function(e) {
    e.preventDefault();
    $.post("ajax_add_department_with_services.php", $(this).serialize(), function() {
        location.reload();
    }).fail(function(xhr) {
        alert("Error: " + xhr.responseText);
    });
});

// Submit edit form
$(".editForm").submit(function(e) {
    e.preventDefault();
    const form = $(this);
    const deptId = form.data("id");
    const formData = form.serialize() + `&id=${deptId}`;
    $.post("ajax_edit_department_with_services.php", formData, function() {
        location.reload();
    }).fail(function(xhr) {
        alert("Error: " + xhr.responseText);
    });
});

// Live search filter
// Updated search logic
$("#searchInput").on("input", function () {
    const val = $(this).val().toLowerCase();
    $(".dept-card").each(function () {
        const searchAttr = $(this).data("search");
        $(this).toggle(searchAttr.includes(val));
    });
});


// Clickable badge filter (multi-select OR)
$(document).on("click", ".service-badge", function () {
    const service = $(this).data("service");
    if (selectedServices.includes(service)) {
        selectedServices = selectedServices.filter(s => s !== service);
        $(this).removeClass("badge-primary").addClass("badge-info");
    } else {
        selectedServices.push(service);
        $(this).removeClass("badge-info").addClass("badge-primary");
    }
    filterDepartments();
});

function filterDepartments() {
    $(".dept-card").each(function () {
        const services = $(this).data("services");
        const matches = selectedServices.some(s => services.includes(s));
        $(this).toggle(selectedServices.length === 0 || matches);
    });
}

$("#clearFilters").click(function () {
    selectedServices = [];
    $(".dept-card").show();
    $(".service-badge").removeClass("badge-primary").addClass("badge-info");
    $("#searchInput").val(""); // clear search input field too
});

</script>
</body>
</html>
