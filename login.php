<?php
session_start();
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user and detect role
    $query = "SELECT auth.id AS auth_id, auth.user_id, auth.role, auth.password, users.first_name, users.last_name
              FROM auth
              INNER JOIN users ON auth.user_id = users.id
              WHERE auth.email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $dbPassword = $user['password'];
        $isPasswordValid = password_verify($password, $dbPassword) || $password === $dbPassword;

        if ($isPasswordValid) {
            // Upgrade plain password to hashed
            if ($password === $dbPassword) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $pdo->prepare("UPDATE auth SET password = :hashedPassword WHERE id = :id")
                    ->execute(['hashedPassword' => $hashedPassword, 'id' => $user['auth_id']]);
            }

            // Set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

            // Redirect to the correct dashboard
            switch ($user['role']) {
                case 'Admin':
                    header('Location: admin_dashboard.php');
                    break;
                case 'LGU Personnel':
                    header('Location: personnel_dashboard.php');
                    break;
                case 'Residents': // Or 'Residents' if you updated it
                    header('Location: residents_dashboard.php');
                    break;
                default:
                    echo "<script>alert('Unknown role. Contact system admin.');</script>";
                    exit();
            }
            exit();
        } else {
            echo "<script>alert('Invalid password!');</script>";
        }
    } else {
        echo "<script>alert('Account not found!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Body Styling */
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)),
                url('images/background.png') no-repeat center center/cover;
            margin: 0;
        }

        /* Container Styling */
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            text-align: center;
            width: 100%
        }

        /* Title Styling */
        .login-container h2 {
            font-family: 'Arial', sans-serif;
            font-size: 22px;
            font-weight: bold;
            color: #5a5cb7;
            margin-bottom: 10px;
        }

        /* Logo and Header Styling */
        .login-logo img {
            width: 50px;
            margin-bottom: 10px;
            border-radius: 30px;
        }

        /* Input Field Styling */
        .login-container .form-control {
            background-color: #f1f3f4;
            border: 1px solid #ddd;
            height: 45px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .login-container input::placeholder {
            color: #888;
        }

        /* Button Styling */
        .login-container .btn-primary {
            width: 100%;
            height: 45px;
            font-size: 16px;
            font-weight: bold;
            background-color: #1a73e8;
            border: none;
            border-radius: 5px;
        }

        .login-container .btn-primary:hover {
            background-color: #135ab6;
        }

        /* Links Styling */
        .login-container a {
            text-decoration: none;
            color: #1a73e8;
            font-size: 14px;
        }

        .login-container a:hover {
            text-decoration: underline;
        }

         /* Eye Icon Styling */
         .show-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
        }

        .form-group {
        position: relative;
    }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Logo and Header -->
        <div class="login-logo">
            <img src="images/logo.png" style="width: 80px; height: 80px;" alt="Logo">
        </div>
        <h2 style="color: #27548A;" >Login to LGU QuickAppoint</h2>

        <!-- Login Form -->
        <form method="POST">
            <div class="form-group">
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                <!-- Unicode Eye Icon -->
                <span class="show-password" onclick="togglePassword()">👁️</span>
                </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>

        <!-- Links Section -->
        <div class="mt-3">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
        <div class="mt-2">
            <small>Don’t have an account yet? <a href="register.php">Register Here</a></small>
        </div>
    </div>
    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        }
    </script>
</body>
</html>
