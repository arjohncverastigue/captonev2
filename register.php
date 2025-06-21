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

    $address = $_POST['address'];
    $birthday = $_POST['birthday'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $civil_status = $_POST['civil_status'];
    $valid_id_type = $_POST['valid_id_type'];

    // Handle file uploads
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $valid_id_image_path = $uploadDir . uniqid() . '_' . basename($_FILES['valid_id_image']['name']);
    $selfie_image_path = $uploadDir . uniqid() . '_' . basename($_FILES['selfie_image']['name']);

    move_uploaded_file($_FILES['valid_id_image']['tmp_name'], $valid_id_image_path);
    move_uploaded_file($_FILES['selfie_image']['tmp_name'], $selfie_image_path);

    try {
        // Step 2: Begin transaction
        $pdo->beginTransaction();

        // Step 3: Insert into users (personal info)
        $userStmt = $pdo->prepare("
            INSERT INTO users (
                first_name, middle_name, last_name,
                address, birthday, age, sex, civil_status,
                valid_id_type, valid_id_image, selfie_image
            ) VALUES (
                :first_name, :middle_name, :last_name,
                :address, :birthday, :age, :sex, :civil_status,
                :valid_id_type, :valid_id_image, :selfie_image
            )
        ");

        $userStmt->execute([
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'address' => $address,
            'birthday' => $birthday,
            'age' => $age,
            'sex' => $sex,
            'civil_status' => $civil_status,
            'valid_id_type' => $valid_id_type,
            'valid_id_image' => $valid_id_image_path,
            'selfie_image' => $selfie_image_path
        ]);

        $user_id = $pdo->lastInsertId();

        // Step 4: Insert into auth (credentials)
        $authStmt = $pdo->prepare("
            INSERT INTO auth (user_id, email, password, role)
            VALUES (:user_id, :email, :password, :role)
        ");
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
    <title>Register | LGU QuickAppoint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            
            padding: 40px;
            border-radius: 10px;
            
            width: 90%;
            max-width: 1300px;
            
        }
        .register-container h2 {
            text-align: center;
            font-weight: bold;
            color: #5a5cb7;
            margin-bottom: 25px;
        }
        .form-control {
            border-radius: 20px;
            padding: 10px 15px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 20px;
            margin: 0 auto;
            width: 25%;
            padding: 10px;
            display: block;
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
<body class="p-4">
    <div class="register-container">
        <h2>Register for LGU QuickAppoint</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <!-- Name Fields -->
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="birthday">Birthday</label>
                        <input type="date" name="birthday" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" name="age" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Account Fields -->
                    <div class="form-group">
                        <label for="sex">Sex</label>
                        <select name="sex" class="form-control" required>
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="civil_status">Civil Status</label>
                        <select name="civil_status" class="form-control" required>
                            <option value="">Select</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="valid_id_type">Valid ID Type</label>
                        <select name="valid_id_type" class="form-control" required>
                            <option value="">Select ID Type</option>
                            <option value="PhilSys ID">PhilSys ID</option>
                            <option value="TIN ID">TIN ID</option>
                            <option value="PhilHealth ID">PhilHealth ID</option>
                            <option value="Driver's License">Driver's License</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="valid_id_image">Upload Valid ID</label>
                        <input type="file" name="valid_id_image" class="form-control-file" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="selfie_image">Upload Selfie</label>
                        <input type="file" name="selfie_image" class="form-control-file" accept="image/*" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
            <a href="index.php" class="login-link">Already have an account? Login Here</a>
        </form>
    </div>
</body>
</html>
