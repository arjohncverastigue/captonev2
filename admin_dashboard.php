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
            background-color: var(--white-color);
            z-index: 100;
        }

        .header_toggle {
            color: var(--first-color);
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
            background-color: var(--first-color);
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
            text-decoration: none;
        }

        .nav_link:hover {
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 5px;
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
    </header>

    <!-- Sidebar -->
    <div class="l-navbar" id="nav-bar">
        <h4 style="text-align: center; color: white;">Admin Menu</h4>
        <nav class="nav">
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
        <h3>Welcome to Admin Dashboard</h3>
        <p>
            <?php if (isset($_SESSION['user_name'])): ?>
                Hello, <strong><?php echo $_SESSION['user_name']; ?></strong>! You have administrative access.
            <?php else: ?>
                Hello, <strong>Admin</strong>! Welcome.
            <?php endif; ?>
        </p>
        <p>Use the sidebar to:</p>
        <ul>
            <li><strong>Create LGU Personnel</strong>: Add new users with LGU Personnel role.</li>
            <li><strong>Manage Departments</strong>: Add, edit, or delete department records.</li>
            <li><strong>View Feedback</strong>: Review feedback submitted by residents.</li>
            <li><strong>View Appointments</strong>: See all appointments and their statuses.</li>
            <li><strong>Manage User Accounts</strong>: View and manage all user accounts.</li>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
