<?php
session_start();
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: Collect form inputs
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'Residents';

    try {
        // Step 2: Begin transaction
        $pdo->beginTransaction();

        // Step 3: Insert into users (personal info)
        $userStmt = $pdo->prepare("INSERT INTO users (first_name, middle_name, last_name) VALUES (:first_name, :middle_name, :last_name)");
        $userStmt->execute([
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name
        ]);
        $user_id = $pdo->lastInsertId();

        // Step 4: Insert into auth (credentials)
        $authStmt = $pdo->prepare("INSERT INTO auth (user_id, email, password, role) VALUES (:user_id, :email, :password, :role)");
        $authStmt->execute([
            'user_id' => $user_id,
            'email' => $email,
            'password' => $password,
            'role' => $role
        ]);

        // Step 5: Commit transaction
        $pdo->commit();

        echo "<script>alert('Registration successful!'); window.location.href='index.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | LGU QuickAppoint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.8)),
                url('images/background.png') no-repeat center center/cover;
            font-family: Arial, sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .register-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 1200px;
        }
        .register-container h2 {
            text-align: center;
            font-weight: bold;
            color: #5a5cb7;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 20px;
            padding: 10px 15px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 20px;
            margin-left: 375px;
            width: 25%;
            padding: 10px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .login-link {
            text-align: center;
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register for LGU QuickAppoint</h2>
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter your first name" required>
                    </div>
                    <div class="form-group">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder="Enter your middle name">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter your last name" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
            <a href="index.php" class="login-link">Already have an account? Login Here</a>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
