<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        :root {
            --header-height: 3rem;
            --nav-width: 68px;
            --first-color:rgb(47, 133, 225);
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
            color: #27548A;
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
            color: #fff;
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
        #sidebar-logo{
            display:block;
            margin:0 auto 10px auto;
            width:120px; height:auto;
            max-width:80%; 
        }
        .border-left-primary {
        border-left: 5px solid #007bff !important;
        }
        .border-left-success {
            border-left: 5px solid #28a745 !important;
        }
        .border-left-warning {
            border-left: 5px solid #ffc107 !important;
        }
        .border-left-danger {
            border-left: 5px solid #dc3545 !important;
        }
        .border-left-info {
            border-left: 5px solid #17a2b8 !important;
        }
        .me-3 {
            margin-right: 1rem;
        }
        .card-header{
            background-image: linear-gradient(to right, #0D92F4, #27548A);
        }
        .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        }
        .card:hover {
        transform: scale(1.03);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
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
        <h4 style="text-align: center; color: white;">Admin Menu</h4>
        <nav class="nav">
            <a href="#" class="nav_link" onclick="loadContent('admin_analytics.php')">
                <i class='bx bx-home-alt'></i> <span>Dashboard</span>
            </a>
            <a href="#" class="nav_link" onclick="loadContent('admin_create_lgu_personnel.php')">
                <i class='bx bx-user-plus'></i> <span>Manage LGU Personnel</span>
            </a>
            <a href="#" class="nav_link" onclick="loadContent('admin_manage_departments.php')">
                <i class='bx bx-building-house'></i> <span>Manage Department</span>
            </a>
            <a href="#" class="nav_link" onclick="loadContent('admin_view_feedback.php')">
                <i class='bx bx-message-rounded-dots'></i> <span>View Feedback</span>
            </a>
            <a href="#" class="nav_link" onclick="loadContent('admin_view_appointments.php')">
                <i class='bx bx-calendar-event'></i> <span>View Appointments</span>
            </a>
            <a href="#" class="nav_link" onclick="loadContent('admin_manage_residents_accounts.php')">
                <i class='bx bx-group'></i> <span>Manage Residents Accounts</span>
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
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class='bx bx-grid-alt'></i> Welcome, Admin!</h3>
            </div>
            <div class="card-body">
                <p class="lead">
                    <?php if (isset($_SESSION['user_name'])): ?>
                        Hello, <strong><?php echo $_SESSION['user_name']; ?></strong>! You have administrative access.
                    <?php else: ?>
                        Hello, <strong>Admin</strong>! Welcome.
                    <?php endif; ?>
                </p>
                <p>Use the sidebar to navigate through administrative features:</p>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="card h-100 shadow-sm border-dark p-3">
                            <a href="#" class="text-dark" style="text-decoration: none;" onclick="loadContent('admin_analytics.php')">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-grid-alt bx-lg text-dark me-3'></i>
                                <div>
                                <h5 class="mb-0">Dashboard</h5>
                                <small>Return to the main admin overview</small>
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-left-primary p-3">
                            <a href="#" class="text-dark" style="text-decoration: none;" onclick="loadContent('admin_create_lgu_personnel.php')">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-user-plus bx-lg text-primary me-3'></i>
                                <div>
                                    <h5 class="mb-0">Create LGU Personnel</h5>
                                    <small>Add new users with LGU Personnel role</small>
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-left-success p-3">
                            <a href="#" class="text-dark" style="text-decoration: none;" onclick="loadContent('admin_manage_departments.php')">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-building-house bx-lg text-success me-3'></i>
                                <div>
                                    <h5 class="mb-0">Manage Departments</h5>
                                    <small>Add, edit, or delete department records</small>
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-left-warning p-3">
                            <a href="#" class="text-dark" style="text-decoration: none;" onclick="loadContent('admin_view_feedback.php')">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-message-rounded-dots bx-lg text-warning me-3'></i>
                                <div>
                                    <h5 class="mb-0">View Feedback</h5>
                                    <small>Review feedback submitted by residents</small>
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-left-danger p-3">
                            <a href="#" class="text-dark" style="text-decoration: none;" onclick="loadContent('admin_view_appointments.php')">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-calendar-event bx-lg text-danger me-3'></i>
                                <div>
                                    <h5 class="mb-0">View Appointments</h5>
                                    <small>See all appointments and their statuses</small>
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-left-info p-3">
                            <a href="#" class="text-dark" style="text-decoration: none;" onclick="loadContent('admin_manage_residents_accounts.php')">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-group bx-lg text-info me-3'></i>
                                <div>
                                    <h5 class="mb-0">Manage User Accounts</h5>
                                    <small>View and manage all resident user accounts</small>
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                        <div class="col-md-6 mb-3">
                        <div class="card h-100 shadow-sm border-left-secondary p-3 hover-card" onclick="window.location.href='logout.php'" style="cursor:pointer;">
                            <div class="d-flex align-items-center">
                                <i class='bx bx-log-out bx-lg text-secondary me-3'></i>
                                <div>
                                    <h5 class="mb-0">Logout</h5>
                                    <small>You can just click here to securely logout</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- row -->
            </div>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
