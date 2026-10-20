<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $result->fetch_assoc();

// ตรวจสอบการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']); // ถ้าไม่เปลี่ยนรหัสผ่าน สามารถเว้นว่างได้
    $occupation = $conn->real_escape_string(trim($_POST['occupation']));

    // ถ้ามีการเปลี่ยนรหัสผ่าน ให้เข้ารหัสใหม่
    $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $user['password'];

    // อัปเดตข้อมูล
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, occupation = ? WHERE user_id = ?");
    $stmt->bind_param("ssssi", $username, $email, $hashed_password, $occupation, $user_id);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>อัปเดตข้อมูลสำเร็จ! <a href='admin_dashboard.php'>กลับไปแดชบอร์ด</a></p>";
        // อัปเดต session ถ้ามีการเปลี่ยน username
        if ($username !== $user['username']) {
            $_SESSION['username'] = $username;
        }
    } else {
        echo "<p style='color: red;'>เกิดข้อผิดพลาด: " . $conn->error . "</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
            background-color: #2e7d32;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #fff;
        }
        .header img {
            width: 40px;
            cursor: pointer;
        }
        .header .menu a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
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
            background-color: #2e7d32;
            color: white;
            text-align: center;
            padding: 15px 20px;
            width: 100%;
            margin-top: 20px;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
        }
        .footer img {
            width: 40px;
            vertical-align: middle;
            margin-right: 5px;
            cursor: pointer;
        }
        .footer span {
            font-size: 14px;
        }

        /* Media Queries for Responsiveness */
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
        <img onclick="window.location.href='index.php'" src="https://img.icons8.com/ios-filled/50/ffffff/leaf.png" alt="Logo">
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
        <h1>Edit Profile</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password (leave blank to keep current):</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="occupation">Occupation:</label>
                <select id="occupation" name="occupation" required>
                    <option value="" <?php echo empty($user['occupation']) ? 'selected' : ''; ?>>เลือกอาชีพ</option>
                    <option value="Doctor" <?php echo $user['occupation'] == 'Doctor' ? 'selected' : ''; ?>>Doctor</option>
                    <option value="Student" <?php echo $user['occupation'] == 'Student' ? 'selected' : ''; ?>>Student</option>
                    <option value="Scientist" <?php echo $user['occupation'] == 'Scientist' ? 'selected' : ''; ?>>Scientist</option>
                    <option value="Researcher" <?php echo $user['occupation'] == 'Researcher' ? 'selected' : ''; ?>>Researcher</option>
                    <option value="Pharmacist" <?php echo $user['occupation'] == 'Pharmacist' ? 'selected' : ''; ?>>Pharmacist</option>
                    <option value="People" <?php echo $user['occupation'] == 'People' ? 'selected' : ''; ?>>People</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit">Update Profile</button>
            </div>
        </form>
        <?php if (isset($message)) echo $message; ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        <img onclick="window.location.href='index.php'" src="https://img.icons8.com/ios-filled/50/ffffff/leaf.png" alt="Logo">
        <span>Center of Excellence in Herbal & Bioactive Compounds | Copyright © 2025</span>
    </div>
</body>
</html>

<?php
$conn->close();
?>