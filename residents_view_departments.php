<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Departments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .calendar-day.available { background-color: #e6ffe6; cursor: pointer; }
        .calendar-day.available:hover { background-color: #ccffcc; }
        .calendar-day.selected { background-color: #28a745 !important; color: white; font-weight: bold; }
        .card:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        #calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }
        .modal-dialog { max-width: 90vw; }
        .modal-body { max-height: 80vh; overflow-y: auto; }
        .badge { font-size: 0.75rem; display: block; }
    </style>
</head>
<body class="p-4">
<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

$query = "SELECT d.*, GROUP_CONCAT(s.service_name SEPARATOR ', ') AS services
          FROM departments d
          LEFT JOIN department_services s ON d.id = s.department_id
          GROUP BY d.id ORDER BY d.name ASC";
$stmt = $pdo->query($query);
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container">
    <h3 class="mb-4">Departments</h3>

    <div class="input-group mb-4">
        <input type="text" class="form-control" id="searchInput" placeholder="Search department or service...">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" id="clearSearch">Clear</button>
        </div>
    </div>

    <div class="row" id="departmentList">
        <?php foreach ($departments as $d): ?>
            <div class="col-md-4 mb-3 department-card">
                <div class="card h-100" data-toggle="modal" data-target="#deptModal<?= $d['id'] ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($d['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($d['description']) ?></p>
                        <div>
                            <?php foreach (explode(',', $d['services']) as $svc): ?>
                                <span class="badge badge-info mr-1 mb-1"><?= htmlspecialchars(trim($svc)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="deptModal<?= $d['id'] ?>" tabindex="-1">
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
                                <?php foreach (explode(',', $d['services']) as $svc): ?>
                                    <li><?= htmlspecialchars(trim($svc)) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button class="btn btn-primary btn-block mt-3" data-toggle="modal" data-target="#appointmentModal" data-dismiss="modal" onclick="openBooking(<?= $d['id'] ?>)">Book Appointment</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal fade" id="appointmentModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="appointment-form" class="modal-content" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title">Book Appointment</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="department_id" id="department_id">
                <input type="hidden" name="available_date_id" id="available_date_id">

                <div class="form-group">
                    <label for="service">Select Service</label>
                    <select class="form-control" name="service" id="service" required></select>
                </div>

                <div class="form-group">
                    <label for="valid_id">Upload Valid ID</label>
                    <input type="file" class="form-control" name="valid_id" id="valid_id" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label>Select Available Date</label>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="prevMonth">Previous</button>
                        <span id="calendar-header" class="font-weight-bold"></span>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="nextMonth">Next</button>
                    </div>
                    <div id="calendar"></div>
                    <div id="slotSelector" class="mt-2"></div>
                </div>

                <div class="form-group">
                    <label for="reason">Reason for Appointment</label>
                    <textarea class="form-control" name="reason" id="reason" required></textarea>
                </div>

                <button type="submit" class="btn btn-success btn-block">Confirm Appointment</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="transactionModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Transaction Number</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center">
        <p>This is your transaction number:</p>
        <h4 id="transactionNumber" class="text-primary font-weight-bold"></h4>
        <p class="mt-3">Please remember it and provide it to the assigned personnel when requested.</p>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let currentMonth = new Date().getMonth() + 1;
let currentYear = new Date().getFullYear();

function openBooking(departmentId) {
    $('#appointmentModal').modal('show');
    $('#department_id').val(departmentId);
    $('#available_date_id').val('');
    $('#calendar').empty();
    $('#slotSelector').empty();

    $.get('get_services_by_department.php', { department_id: departmentId }, function(data) {
        $('#service').html(data);
    });

    loadCalendar(departmentId);
}

function loadCalendar(departmentId) {
    $.get('get_available_dates.php', { department_id: departmentId, month: currentMonth, year: currentYear }, function(data) {
        const availableDates = JSON.parse(data);
        generateCalendar(availableDates);
    });
}

function generateCalendar(availableDates) {
    const calendar = $('#calendar');
    calendar.empty();

    const firstDay = new Date(currentYear, currentMonth - 1, 1);
    const lastDate = new Date(currentYear, currentMonth, 0).getDate();
    const startDay = firstDay.getDay();
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    $('#calendar-header').text(firstDay.toLocaleString('default', { month: 'long' }) + ' ' + currentYear);

    days.forEach(day => calendar.append(`<div class='font-weight-bold text-center'>${day}</div>`));
    for (let i = 0; i < startDay; i++) calendar.append('<div></div>');

    for (let day = 1; day <= lastDate; day++) {
        const dateStr = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const data = availableDates[dateStr] || null;
        const div = $(`<div class='calendar-day text-center ${data ? "available" : ""}' data-date='${dateStr}'>${day}</div>`);

        if (data) {
            div.append(`<div class='badge badge-success'>AM: ${data.am_slots - data.am_booked}</div>`);
            div.append(`<div class='badge badge-info'>PM: ${data.pm_slots - data.pm_booked}</div>`);
            div.click(function () {
                $('.calendar-day').removeClass('selected');
                $(this).addClass('selected');
                $('#slotSelector').html(`
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="slot_period" value="am" data-id="${data.id}" required>
                    <label class="form-check-label">AM Slot</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="slot_period" value="pm" data-id="${data.id}" required>
                    <label class="form-check-label">PM Slot</label>
                  </div>`);
            });
        }
        calendar.append(div);
    }
}

$(document).on('change', 'input[name="slot_period"]', function() {
    const selectedId = $(this).data('id');
    $('#available_date_id').val(selectedId);
});

$('#prevMonth').click(() => { currentMonth--; if (currentMonth < 1) { currentMonth = 12; currentYear--; } loadCalendar($('#department_id').val()); });
$('#nextMonth').click(() => { currentMonth++; if (currentMonth > 12) { currentMonth = 1; currentYear++; } loadCalendar($('#department_id').val()); });

$('#appointment-form').submit(function(e) {
    e.preventDefault();

    const selectedSlot = $('input[name="slot_period"]:checked');
    const selectedSlotId = selectedSlot.data('id');
    const slotPeriod = selectedSlot.val();

    if (!selectedSlot.length || !selectedSlotId || !slotPeriod) {
        alert("Please select a date and slot period.");
        return;
    }

    $('#available_date_id').val(selectedSlotId);

    const formData = new FormData(this);
    formData.append('slot_period', slotPeriod);

    $.ajax({
        url: 'residents_submit_appointment.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function(res) {
            if (res.status === 'success') {
                $('#appointmentModal').modal('hide');
                $('#transactionNumber').text(res.appointment_id || 'N/A');
                $('#transactionModal').modal('show');
                $('#appointment-form')[0].reset();
                $('#slotSelector').empty();
            } else {
                alert(res.message || 'Something went wrong.');
            }
        },
        error: function(xhr) {
            alert("Error booking appointment: " + xhr.responseText);
        }
    });
});

$('#searchInput').on('input', function() {
    const val = $(this).val().toLowerCase();
    $('.department-card').each(function() {
        const text = $(this).text().toLowerCase();
        $(this).toggle(text.includes(val));
    });
});

$('#clearSearch').click(function () {
    $('#searchInput').val('');
    $('.department-card').show();
});
</script>
</body>
</html>
