<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'LGU Personnel') {
    if ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
        exit;
    } else {
        echo "<div class='alert alert-danger'>Unauthorized access.</div>";
        exit();
    }
}

// Fetch the department assigned to the logged-in LGU Personnel
$userId = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("SELECT department_id FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user || !$user['department_id']) {
        die("<div class='alert alert-danger'>Assigned department not found.</div>");
    }
    $departmentId = $user['department_id'];
} catch (Exception $e) {
    die("<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date_time'])) {
    $dateTimes = $_POST['date_time'];

    try {
        $stmt = $pdo->prepare("INSERT INTO available_dates (department_id, date_time) VALUES (:dept, :dt)");
        foreach ($dateTimes as $dt) {
            if (!empty($dt)) {
                $stmt->execute(['dept' => $departmentId, 'dt' => $dt]);
            }
        }

        if ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode(['status' => 'success', 'message' => 'Available dates added successfully.']);
            exit;
        }

        $success = "Available dates added successfully.";
    } catch (Exception $e) {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            exit;
        }
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!-- HTML and JS stay mostly the same, except remove department select -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Available Dates</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        #calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            margin-top: 10px;
        }

        .calendar-day {
            padding: 15px;
            border: 1px solid #ccc;
            text-align: center;
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .calendar-day.disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
            color: #999;
        }

        .calendar-day:hover:not(.disabled):not(.selected) {
            background-color: #d4edda;
        }

        .selected {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }

        .calendar-header {
            font-weight: bold;
            background-color: #e9ecef;
            text-align: center;
            padding: 10px 0;
        }

        .month-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .month-nav button {
            margin: 5px;
        }
    </style>
</head>
<body class="p-4">
<div class="container">
    <h4>Create Available Dates</h4>

    <div id="response-msg"></div>

    <form method="post" id="available-dates-form">
        <div class="form-group">
            <label for="selected-dates">Selected Dates and Times:</label>
            <div id="dateInputs"></div>
        </div>

        <div class="form-group">
            <label>Select Dates from Calendar</label>
            <div class="month-nav">
                <button type="button" id="prevMonth" class="btn btn-outline-secondary btn-sm">Previous</button>
                <h5 id="calendar-header" class="mb-0"></h5>
                <button type="button" id="nextMonth" class="btn btn-outline-secondary btn-sm">Next</button>
            </div>
            <div id="calendar"></div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Submit Available Dates</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();

function generateCalendar(month, year) {
    const calendar = $('#calendar');
    calendar.empty();

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const firstDay = new Date(year, month, 1);
    const lastDate = new Date(year, month + 1, 0).getDate();
    const startDay = firstDay.getDay();

    const monthName = firstDay.toLocaleString('default', { month: 'long' });
    $('#calendar-header').text(`${monthName} ${year}`);

    const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    daysOfWeek.forEach(day => {
        calendar.append(`<div class="calendar-header">${day}</div>`);
    });

    for (let i = 0; i < startDay; i++) {
        calendar.append('<div></div>');
    }

    for (let date = 1; date <= lastDate; date++) {
        const dateObj = new Date(year, month, date);
        const dayOfWeek = dateObj.getDay(); // 0 = Sunday, 6 = Saturday
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;

        const cell = $(`<div class="calendar-day">${date}</div>`);

        if (dateObj < today || dayOfWeek === 0 || dayOfWeek === 6) {
            cell.addClass('disabled');
        } else {
            cell.attr('data-date', dateStr);
            cell.click(function () {
                if (!$(this).hasClass('selected')) {
                    $(this).addClass('selected');
                    const inputHTML = `
                        <div class="input-group mb-2" data-date="${dateStr}">
                            <input type="datetime-local" name="date_time[]" class="form-control" value="${dateStr}T09:00">
                            <div class="input-group-append">
                                <button class="btn btn-danger remove-btn" type="button">Remove</button>
                            </div>
                        </div>
                    `;
                    $('#dateInputs').append(inputHTML);
                }
            });
        }

        calendar.append(cell);
    }
}

$('#prevMonth').click(() => {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    generateCalendar(currentMonth, currentYear);
});

$('#nextMonth').click(() => {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    generateCalendar(currentMonth, currentYear);
});

$(document).on('click', '.remove-btn', function () {
    const parent = $(this).closest('.input-group');
    const date = parent.data('date');
    $(`.calendar-day[data-date="${date}"]`).removeClass('selected');
    parent.remove();
});

$(document).ready(() => {
    generateCalendar(currentMonth, currentYear);

    $('#available-dates-form').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: 'create_available_dates.php',
            method: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                let result;
                try {
                    result = JSON.parse(res);
                } catch {
                    $('#response-msg').html('<div class="alert alert-danger">Unexpected server response.</div>');
                    return;
                }

                if (result.status === 'success') {
                    $('#response-msg').html('<div class="alert alert-success">' + result.message + '</div>');
                    $('#dateInputs').empty();
                    $('.calendar-day.selected').removeClass('selected');
                } else {
                    $('#response-msg').html('<div class="alert alert-danger">' + result.message + '</div>');
                }
            },
            error: function () {
                $('#response-msg').html('<div class="alert alert-danger">Error submitting form.</div>');
            }
        });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
