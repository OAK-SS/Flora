<?php

require 'db_connect.php';

// ตรวจสอบการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; 
    $occupation = $conn->real_escape_string($_POST['occupation']);


    // ตรวจสอบข้อมูลไม่ว่าง
    if (!empty($username) && !empty($email) && !empty($password) && !empty($occupation)) {
        // ตรวจสอบว่าอีเมลซ้ำหรือไม่
        $check_email = $conn->query("SELECT email FROM users WHERE email = '$email'");
        if ($check_email->num_rows == 0) {
            // เข้ารหัสรหัสผ่านด้วย password_hash
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, email, password, occupation) VALUES ('$username', '$email', '$hashed_password', '$occupation')";
            if ($conn->query($sql) === TRUE) {
                echo "<p style='color: green;'>สมัครสมาชิกสำเร็จ! <a href='login.php'>ไปที่หน้าเข้าสู่ระบบ</a></p>";
            } else {
                echo "<p style='color: red;'>เกิดข้อผิดพลาด: " . $conn->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'>อีเมลนี้ถูกใช้ไปแล้ว!</p>";
        }
    } else {
        echo "<p style='color: red;'>กรุณากรอกข้อมูลให้ครบถ้วน!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }
        body {
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.5;
        }
        .header {
            background-color:rgb(32, 84, 35);
            color: white;
            padding: 30px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #fff;
        }

        .header img {
            width: 100px;
            left: 3px;
            position: absolute;
            cursor: pointer;
        }

        .header .menu a {
            color: #fff;
            text-decoration: none;
            margin: 100px;
            margin-right: 1px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .header .menu a:hover {
            color: #ff9800;
        }

        .header .auth a {
            color: #fff;
            text-decoration: none;
            margin-left: 15px;
            font-weight: 600;
            text-transform: uppercase;
            border: 1px solid #fff;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .header .auth a:hover {
            background-color: #fff;
            color: #2e7d32;
        }
        .content {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .content h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2e7d32;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: none;
            background-color: #2e7d32;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-group button:hover {
            background-color: #1b5e20;
        }
        .footer {
            background-color: rgb(32, 84, 35);
            color: white;
            text-align: center;
            padding: 93px;
            margin-top: 20px;
            width: 100%;
            display: flex;
            align-items: center;
            position: absolute;
            left: 0;
        }

        .footer img {
            position: absolute;
            left: 20px;
            /* top: 1150px; */
            width: 210px;
            vertical-align: middle;
            margin-right: 5px;
            cursor: pointer;
        }

        .footer span {
            font-size: 14px;
            margin: auto;
        }
       
        @media (max-width: 768px) {
            .header .menu a {
                margin: 5px 10px;
            }
            .header .auth a {
                margin-left: 10px;
            }
            .content {
                margin: 10px;
                padding: 15px;
            }
            .content h1 {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            .header {
                flex-direction: column;
                padding: 10px;
            }
            .header .menu {
                margin-bottom: 10px;
            }
            .header .menu a {
                margin: 5px;
            }
            .header .auth a {
                margin: 5px 0;
            }
            .content h1 {
                font-size: 20px;
            }
            .form-group input, .form-group select {
                font-size: 14px;
            }
            .form-group button {
                font-size: 14px;
            }
            .footer img {
                width: 30px;
            }
            .footer span {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <img onclick="window.location.href='index.php'" src="img/Center_of_Excellence1-removebg-preview.png" alt="Logo">
        <div class="menu">
            <a href="index.php">HOME</a>
            <a href="plants.php">PLANTS</a>
            <a href="compounds.php">COMPOUNDS</a>
            <a href="upload.php">UPLOAD</a>
            <a href="edit_data.php">EDIT</a>
        </div>
        <div class="auth">
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <h1>Register</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="occupation">Occupation:</label>
                <select id="occupation" name="occupation" required>
                    <option value="">Choose Occupation</option>
                    <option value="Doctor">Doctor</option>
                    <option value="Student">Student</option>
                    <option value="Scientist">Scientist</option>
                    <option value="Researcher">Researcher</option>
                    <option value="Pharmacist">Pharmacist</option>
                    <option value="People">People</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit">Register</button>
            </div>
        </form>
        <?php if (isset($message)) echo $message; ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        <img onclick="window.location.href='index.php'" src="img/Center_of_Excellence1-removebg-preview.png" alt="Logo">
        <span>Center of Excellence in Herbal & Bioactive Compounds | Copyright © 2025</span>
    </div>
</body>
</html>

<?php
$conn->close();
?>