<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
// Include or create connection
$host = "localhost";
$username = "root";
$password = "";
$database = "lgu_q_a";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to count residents
$sql = "SELECT COUNT(*) AS resident_count FROM auth WHERE role = 'Residents'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$residentCount = $row['resident_count'];

// Query to count total appointments
$sqlAppointments = "SELECT COUNT(*) AS total_appointments FROM appointments";
$resultAppointments = mysqli_query($conn, $sqlAppointments);
$rowAppointments = mysqli_fetch_assoc($resultAppointments);
$appointmentCount = $rowAppointments['total_appointments'];

$feedback_result = "SELECT COUNT(*) AS total_feedback FROM feedback";
$feedback_row = mysqli_query($conn, $feedback_result);
$totalFeedbacks = mysqli_fetch_assoc($feedback_row)['total_feedback'];

// Count completed
$sqlCompleted = "SELECT COUNT(*) AS completed_count FROM appointments WHERE status = 'Completed'";
$resultCompleted = mysqli_query($conn, $sqlCompleted);
$completedCount = mysqli_fetch_assoc($resultCompleted)['completed_count'];

// Count pending
$sqlPending = "SELECT COUNT(*) AS pending_count FROM appointments WHERE status = 'Pending'";
$resultPending = mysqli_query($conn, $sqlPending);
$pendingCount = mysqli_fetch_assoc($resultPending)['pending_count'];

//
$dept_labels = [];
$dept_counts = [];

$sql = "
    SELECT d.name AS department_name, COUNT(a.id) AS total_appointments
    FROM departments d
    LEFT JOIN appointments a ON a.department_id = d.id
    GROUP BY d.id
    ORDER BY d.name ASC;
";

$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $dept_labels[] = $row['department_name'];
    $dept_counts[] = $row['total_appointments'];
}

$sql = "
    SELECT 
        MONTH(requested_at) AS month,
        COUNT(id) AS count
        FROM appointments
        WHERE YEAR(requested_at) = YEAR(CURDATE())
        GROUP BY MONTH(requested_at)
        ORDER BY MONTH(requested_at);
";

$result = mysqli_query($conn, $sql);

// Initialize all months with 0
$monthlyData = array_fill(1, 12, 0);

while ($row = mysqli_fetch_assoc($result)) {
    $monthlyData[(int)$row['month']] = (int)$row['count'];
}

// Define month labels
$monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

?>
<body>
  <h2 class="mb-4 text-center text-primary d-flex align-items-center justify-content-center" style="gap: 10px; font-weight: 600; font-size: 2rem;">
    <i class='bx bxs-dashboard bx-tada' style="font-size: 2.5rem;"></i>
    Admin Dashboard
  </h2>

<!-- Analytics Cards -->
<div class="row">

<!-- Total Appointments -->
<div class="col-md-4 mb-3">
<div class="card shadow-sm border-left-primary p-3">
<a href="#" class="text-dark" style="text-decoration: none; " onclick="loadContent('admin_view_appointments.php')">
<div class="d-flex justify-content-between align-items-center">
    <div>
    <h6>Total Appointments</h6>
    <h3 class="text-primary">
        <?php echo number_format($appointmentCount); ?>
    </h3>
    </div>
    <i class='bx bx-calendar-check bx-lg text-primary'></i>
</div>
</a>
</div>
</div>


<!-- Registered Residents -->
<div class="col-md-4 mb-3">
<div class="card shadow-sm border-left-success p-3">
<a href="#" class="text-dark" style="text-decoration: none; " onclick="loadContent('admin_manage_residents_accounts.php')">
<div class="d-flex justify-content-between align-items-center">
    <div>
    <h6>Registered Residents</h6>
    <h3 class="text-success">
        <?php echo number_format($residentCount); ?>
    </h3>
    </div>
    <i class='bx bx-user bx-lg text-success'></i>
</div>
</a>
</div>
</div>

<!-- Feedback Entries -->
<div class="col-md-4 mb-3">
<div class="card shadow-sm border-left-warning p-3">
<a href="#" class="text-dark" style="text-decoration: none; " onclick="loadContent('admin_view_feedback.php')">
<div class="d-flex justify-content-between align-items-center">
    <div>
    <h6>Total Feedbacks</h6>
    <h3 class="text-warning">
        <?php echo $totalFeedbacks; ?>
    </h3>
    </div>
    <i class='bx bx-message-square-dots bx-lg text-warning'></i>
</div>
</div>
</a>
</div>

</div>

<!-- Charts -->
<div class="row">

<!-- Appointments by Department -->
<div class="col-md-6 mb-3">
<div class="card p-3 shadow-sm">
    <h6>Appointments by Department</h6>
    <canvas id="deptChart"></canvas>
</div>
</div>

<!-- Monthly Appointments -->
<div class="col-md-6 mb-3">
<div class="card p-3 shadow-sm">
    <h6>Monthly Appointment Trend</h6>
    <canvas id="monthChart"></canvas>
</div>
</div>

</div>
</body>
<!-- Chart.js Script -->
<canvas id="monthChart" height="100"></canvas>
<canvas id="deptChart" height="100"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Department-wise Appointments (Bar Chart)
    var ctx1 = document.getElementById('deptChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
        labels: <?php echo json_encode($dept_labels); ?>,
        datasets: [{
            label: 'Appointments',
            data: <?php echo json_encode($dept_counts); ?>,
            backgroundColor: 'rgba(47, 133, 225, 0.7)',
            borderRadius: 5
        }]
        },
        options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
            beginAtZero: true,
            ticks: {
                precision: 0
            }
            }
        }
        }
    });

  // Monthly Appointment Trend (Line Chart)
    var ctx2 = document.getElementById('monthChart').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
        labels: <?php echo json_encode(array_slice($monthLabels, 0, count($monthlyData))); ?>,
        datasets: [{
            label: 'Appointments',
            data: <?php echo json_encode(array_values(array_slice($monthlyData, 0, 12))); ?>,
            backgroundColor: 'rgba(13, 146, 244, 0.2)',
            borderColor: 'rgba(13, 146, 244, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
        },
        options: {
        responsive: true,
        scales: {
            y: {
            beginAtZero: true,
            ticks: { precision: 0 }
            }
        }
        }
    });
</script>
<style>
    .card{
         transition: transform 0.2s ease, box-shadow 0.2s ease;
         box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease; /* smooth effect */
    }
</style>
