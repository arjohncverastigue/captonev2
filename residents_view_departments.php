<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

$query = "SELECT id, name, description, services FROM departments";
$stmt = $pdo->query($query);
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Department List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .container { margin-top: 50px; }
        .modal-content { background-color: rgba(255, 255, 255, 0.95); }

        #calendar-container {
            text-align: center;
        }

        #calendar-header {
            font-weight: bold;
            margin-bottom: 10px;
        }

        #calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .calendar-day {
            border: 1px solid #ccc;
            padding: 10px;
            min-height: 60px;
            cursor: pointer;
        }

        .calendar-day div {
            font-size: 0.75em;
        }

        .available {
            background-color: #d4edda;
        }

        .selected {
            border: 2px solid #28a745;
        }

        .calendar-header {
            font-weight: bold;
            background-color: #f8f9fa;
            padding: 5px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Departments</h2>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Department Name</th>
            <th>Description</th>
            <th>Services</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($departments) > 0): ?>
            <?php foreach ($departments as $department): ?>
                <tr>
                    <td><?= htmlspecialchars($department['name']) ?></td>
                    <td><?= htmlspecialchars($department['description']) ?></td>
                    <td><?= htmlspecialchars($department['services']) ?></td>
                    <td>
                        <button class="btn btn-primary btn-sm book-btn"
                                data-toggle="modal"
                                data-target="#appointmentModal"
                                data-department-id="<?= $department['id'] ?>">
                            Book an Appointment
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center">No departments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="appointment-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book an Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="department_id" id="department_id">
                    <input type="hidden" name="available_date_id" id="available_date_id" required>

                    <div class="form-group">
                        <label>Select an Available Date</label>
                        <div id="calendar-container">
                            <div id="calendar-header"></div>
                            <div id="calendar">
                                <!-- Calendar will be generated here -->
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reason">Reason for Appointment (optional)</label>
                        <textarea class="form-control" id="reason" name="reason"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-block">Confirm Appointment</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function generateCalendar(availableDates) {
    const calendar = $('#calendar');
    calendar.empty();

    const today = new Date();
    const year = today.getFullYear();
    const month = today.getMonth();

    const monthName = today.toLocaleString('default', { month: 'long' });
    $('#calendar-header').text(`${monthName} ${year}`);

    const firstDay = new Date(year, month, 1);
    const lastDate = new Date(year, month + 1, 0).getDate();
    const startDay = firstDay.getDay();

    const availableSet = new Set();
    const dateMap = {};
    const now = new Date().setHours(0, 0, 0, 0);

    availableDates.forEach(d => {
        const dateOnly = new Date(d.date);
        dateOnly.setHours(0, 0, 0, 0);
        if (dateOnly >= now) {
            availableSet.add(d.date);
            dateMap[d.date] = d.id;
        }
    });

    // Weekday headers
    const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    daysOfWeek.forEach(day => {
        calendar.append(`<div class="calendar-header">${day}</div>`);
    });

    // Empty cells before first day
    for (let i = 0; i < startDay; i++) {
        calendar.append('<div></div>');
    }

    for (let date = 1; date <= lastDate; date++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
        const isAvailable = availableSet.has(dateStr);

        const div = $(`<div class="calendar-day ${isAvailable ? 'available' : ''}" data-date="${dateStr}">
                          ${date} ${isAvailable ? '<div class="text-success">Available</div>' : ''}
                      </div>`);

        if (isAvailable) {
            div.click(function () {
                $('.calendar-day').removeClass('selected');
                $(this).addClass('selected');
                $('#available_date_id').val(dateMap[dateStr]);
            });
        }

        calendar.append(div);
    }
}

$('#appointmentModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var departmentId = button.data('department-id');
    $('#department_id').val(departmentId);

    $.get('get_available_dates.php', { department_id: departmentId }, function(data) {
        const dates = JSON.parse(data);
        generateCalendar(dates);
    });
});

$('#appointment-form').submit(function(event) {
    event.preventDefault();

    if (!$('#available_date_id').val()) {
        alert("Please select an available date.");
        return;
    }

    $.ajax({
        url: 'residents_submit_appointment.php',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            const result = JSON.parse(response);
            alert(result.message);
            if (result.status === 'success') {
                $('#appointmentModal').modal('hide');
                location.reload();
            }
        },
        error: function() {
            alert("Something went wrong while booking.");
        }
    });
});
</script>
</body>
</html>
