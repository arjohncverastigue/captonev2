<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Residents') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident's Dashboard</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        :root {
            --header-height: 3rem;
            --nav-width: 68px;
            --first-color: #4723D9;
            --first-color-light: #AFA5D9;
            --white-color: #F7F6FB;
            --body-font: 'Nunito', sans-serif;
            --normal-font-size: 1rem;
        }

        body {
            margin: var(--header-height) 0 0 0;
            padding: 0;
            font-family: var(--body-font);
            background: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url('images/background.png') no-repeat center center/cover;
            background-attachment: fixed;
        }

        .header {
            width: 100%;
            height: var(--header-height);
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            background-image: linear-gradient(to right, #0D92F4, #27548A);
            z-index: 100;
        }

        .header_toggle {
            color:  #27548A;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .header_img img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
        }

        .l-navbar {
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100vh;
            background-color: #0D92F4;
            padding: 1rem 0;
            transition: 0.5s;
            z-index: 100;
        }
        .nav h4 {
            margin-top: 20px;
            text-align: center;
            color: #1a3b96;
        }
        .nav {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .nav_link {
            color: var(--white-color);
            padding: 1rem 1.5rem;
            text-decoration: none !important;
        }

        .nav_link:hover {
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 5px;
            text-decoration: none !important;
        }

        .show {
            left: 0;
        }

        .body-pd {
            padding-left: 250px;
        }

        .content-area {
            margin-top: var(--header-height);
            padding: 10px;
            transition: 0.5s;
        }
        .card{
         transition: transform 0.2s ease, box-shadow 0.2s ease;
         box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease; /* smooth effect */
        }
        .card-header{
            background-image: linear-gradient(to right, #0D92F4, #27548A);
        }
        #sidebar-logo{
            display:block;
            margin:0 auto 10px auto;
            width:120px; height:auto;
            max-width:80%; 
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggle = document.getElementById('header-toggle');
            const nav = document.getElementById('nav-bar');
            const bodyPd = document.getElementById('body-pd');
            const contentArea = document.getElementById('content-area');

            toggle.addEventListener('click', () => {
                nav.classList.toggle('show');
                bodyPd.classList.toggle('body-pd');
            });

            contentArea.addEventListener('click', () => {
                if (nav.classList.contains('show')) { 
                    nav.classList.remove('show');
                    bodyPd.classList.remove('body-pd');
                }
            });
        });

        function loadContent(page) {
            $("#content-area").load(page);
        }
    </script>
</head>
<body id="body-pd">
    <!-- Header -->
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>

        <!-- Avatar icon for profile -->
        <div onclick="loadContent('profile.php')" style="cursor: pointer;">
            <img src="images/default_avatar.png" alt="Avatar" class="rounded-circle" width="35" height="35" title="My Profile">
        </div>
    </header>

    <!-- Sidebar -->
    <div class="l-navbar" id="nav-bar">
        <img src="images/Unisan_logo.png" id= "sidebar-logo" alt="Sidebar Logo" class="header_img">
        <h4 style="text-align: center; color: white;">Residents Menu</h4>
        <nav class="nav">
            <a href="#" class="nav_link" onclick="loadContent('residents_matching_appointments.php')">
                <i class='bx bx-calendar'></i> <span>My Appointments</span>
            </a>
            <a href="#" class="nav_link" onclick="loadContent('residents_view_departments.php')">
                <i class='bx bx-user'></i> <span>View Departments</span>
            </a>
            <a href="#" class="nav_link" onclick="loadContent('residents_select_form.php')">
                <i class='bx bx-message-square'></i> <span>Feedback</span>
            </a>

            <a href="logout.php" class="nav_link">
                <i class='bx bx-log-out'></i> <span>Logout</span>
            </a>
        </nav>
    </div>

    <!-- Content Area -->
<div class="content-area" id="content-area">
    <div class="container mt-4">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-info text-white">
                <h3 class="mb-0"><i class='bx bx-home-alt'></i> Welcome to Resident Dashboard</h3>
            </div>
            <div class="card-body">
                <p class="lead">
                    <?php if (isset($_SESSION['user_name'])): ?>
                        Hello, <strong><?php echo $_SESSION['user_name']; ?></strong>! Welcome to your personal dashboard.
                    <?php else: ?>
                        Hello, <strong>Resident</strong>! Welcome to your personal dashboard.
                    <?php endif; ?>
                </p>
                <p>Use the menu on the left or the options below to manage your appointments and provide feedback:</p>

                <div class="row">
                    <!-- View Appointments -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-left-primary p-3 hover-card" onclick="loadContent('resident_view_appointments.php')" style="cursor: pointer;">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-calendar bx-lg text-primary me-3'></i>
                                <div>
                                    <h5 class="mb-0">View Appointments</h5>
                                    <small>Check status and details of past or upcoming appointments</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- View Departments -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-left-success p-3 hover-card" onclick="loadContent('view_departments.php')" style="cursor: pointer;">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-building-house bx-lg text-success me-3'></i>
                                <div>
                                    <h5 class="mb-0">View Departments</h5>
                                    <small>Explore departments and request appointments</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Feedback -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-left-warning p-3 hover-card" onclick="loadContent('submit_feedback.php')" style="cursor: pointer;">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-message-square-dots bx-lg text-warning me-3'></i>
                                <div>
                                    <h5 class="mb-0">Resident Feedback</h5>
                                    <small>Share your experience with past appointments</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logout -->
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-left-danger p-3 hover-card" onclick="window.location.href='logout.php'" style="cursor:pointer;">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-log-out bx-lg text-danger me-3'></i>
                                <div>
                                    <h5 class="mb-0">Logout</h5>
                                    <small>Click here to safely logout of your account</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- row -->
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
