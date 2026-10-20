<?php
session_start(); 
require 'db_connect.php';


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ข้อมูลพืช
    $common_name = $conn->real_escape_string($_POST['common_name']);
    $scientific_name = $conn->real_escape_string($_POST['scientific_name']);
    $family = $conn->real_escape_string($_POST['family']);
    $description = $conn->real_escape_string($_POST['plant_description']);
    $medical_use = $conn->real_escape_string($_POST['medical_use']);
    $plant_image = $_FILES['plant_image']['name'];
    $plant_image_tmp = $_FILES['plant_image']['tmp_name'];

    // ข้อมูลสารประกอบ
    $compound_name = $conn->real_escape_string($_POST['compound_name']);
    $molecular_formula = $conn->real_escape_string($_POST['molecular_formula']);
    $compound_type = $conn->real_escape_string($_POST['compound_type']);
    $properties = $conn->real_escape_string($_POST['properties']);
    $compound_image = $_FILES['compound_image']['name'];
    $compound_image_tmp = $_FILES['compound_image']['tmp_name'];

    $upload_dir = "img/";
    if (!empty($plant_image)) {
        $plant_image_path = $upload_dir . basename($plant_image);
        move_uploaded_file($plant_image_tmp, $plant_image_path);
    } else {
        $plant_image_path = null;
    }
    if (!empty($compound_image)) {
        $compound_image_path = $upload_dir . basename($compound_image);
        move_uploaded_file($compound_image_tmp, $compound_image_path);
    } else {
        $compound_image_path = null;
    }

    // บันทึกข้อมูลลงฐานข้อมูล
    $stmt_plant = $conn->prepare("INSERT INTO plants (common_name, scientific_name, family, plant_description, plant_image) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE common_name = VALUES(common_name), scientific_name = VALUES(scientific_name), family = VALUES(family), plant_description = VALUES(plant_description), plant_image = VALUES(plant_image)");
    $stmt_plant->bind_param("sssss", $common_name, $scientific_name, $family, $description, $plant_image_path);
    $plant_success = $stmt_plant->execute();

    $plant_id = $conn->insert_id; 
        if ($plant_success) {
            $stmt_medical = $conn->prepare("INSERT INTO medical_uses (plant_id, medical_use) VALUES (?, ?) ON DUPLICATE KEY UPDATE medical_use = VALUES(medical_use)");
            $stmt_medical->bind_param("is", $plant_id, $medical_use);
            $medical_success = $stmt_medical->execute();
            $stmt_medical->close();
        }

    $plant_id = $conn->insert_id; // รับ plant_id ที่เพิ่งเพิ่ม
    if ($plant_success && !empty($compound_name)) {
        $stmt_compound = $conn->prepare("INSERT INTO compounds (compound_name, molecular_formula, compound_type, properties, compound_image) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE compound_name = VALUES(compound_name), molecular_formula = VALUES(molecular_formula), compound_type = VALUES(compound_type), properties = VALUES(properties), compound_image = VALUES(compound_image)");
        $stmt_compound->bind_param("sssss", $compound_name, $molecular_formula, $compound_type, $properties, $compound_image_path);
        $compound_success = $stmt_compound->execute();

         if ($compound_success) {
            if ($conn->insert_id > 0) {
                $compound_id = $conn->insert_id;
            } else {
                $stmt_find_compound = $conn->prepare("SELECT compound_id FROM compounds WHERE compound_name = ?");
                $stmt_find_compound->bind_param("s", $compound_name);
                $stmt_find_compound->execute();
                $stmt_find_compound->bind_result($compound_id);
                $stmt_find_compound->fetch();
                $stmt_find_compound->close();
            }
        }
    }

    if ($plant_success && $medical_success && (!$compound_name || $compound_success)) {
        
        echo "<script>
            alert('Add Data Success');
            window.location.href = 'index.php';
        </script>";
    } else {
        echo "<p style='color: red;'>เกิดข้อผิดพลาดในการอัปโหลด: " . $conn->error . "</p>";
    }
    $stmt_plant->close();
    if (isset($stmt_compound)) $stmt_compound->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Data</title>
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
            background-color: rgb(32, 84, 35);
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
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .content h1 {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .content h2 {
            font-size: 20px;
            color: #666;
            margin-top: 20px;
            margin-bottom: 10px;
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
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            box-sizing: border-box;
        }
        .form-group input[type="file"] {
            padding: 5px;
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
                font-size: 28px;
            }
            .content h2 {
                font-size: 18px;
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
                font-size: 24px;
            }
            .content h2 {
                font-size: 16px;
            }
            .form-group input, .form-group select, .form-group textarea {
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
            <a href="dashbord.php">EDIT</a>
        </div>
        <div class="auth">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                <a href="edit_profile.php" onclick="event.preventDefault(); window.location.href='edit_profile.php';"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                <a href="logout.php" style="border: 1px solid #fff; padding: 5px 10px; border-radius: 5px; margin-left: 10px;">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <h1>Upload Data</h1>
        <h2>Step1. Add Plant Information</h2>
        <form method="POST" enctype="multipart/form-data" action="">
            <div class="form-group">
                <label for="common_name">Common Name</label>
                <input type="text" id="common_name" name="common_name" required>
            </div>
            <div class="form-group">
                <label for="scientific_name">Scientific Name</label>
                <input type="text" id="scientific_name" name="scientific_name" required>
            </div>
            <div class="form-group">
                <label for="family">Family</label>
                <input type="text" id="family" name="family" required>
            </div>
            <div class="form-group">
                <label for="plant_description">Description</label>
                <textarea id="plant_description" name="plant_description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="plant_image">Image</label>
                <input type="file" id="plant_image" name="plant_image" accept="image/*">
            </div>

            <h2>Step2. Add Compound Information</h2>
            <div class="form-group">
                <label for="compound_name">Compound Name</label>
                <input type="text" id="compound_name" name="compound_name">
            </div>
            <div class="form-group">
                <label for="molecular_formula">Molecular Formula</label>
                <input type="text" id="molecular_formula" name="molecular_formula">
            </div>
            <div class="form-group">
                <label for="compound_type">Compound Type</label>
                <input type="text" id="compound_type" name="compound_type">
            </div>
            <div class="form-group">
                <label for="properties">Properties</label>
                <input type="text" id="properties" name="properties">
            </div>
            <div class="form-group">
                <label for="compound_image">Image</label>
                <input type="file" id="compound_image" name="compound_image" accept="image/*">
            </div>

            <h2>Step3. Add Medical Information</h2>
            <div class="form-group">
                <label for="medical_use">Medical Use</label>
                <textarea id="medical_use" name="medical_use"></textarea>
            </div>

            <div class="form-group">
                <button type="submit">Submit</button>
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