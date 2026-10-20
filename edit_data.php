<?php
session_start(); 
require 'db_connect.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$plant_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ดึงข้อมูลพืชจากฐานข้อมูล
$plant = null;
$result = $conn->query("SELECT * FROM plants WHERE plant_id = $plant_id");
if ($result && $result->num_rows > 0) {
    $plant = $result->fetch_assoc();
} else {
    die("<p style='color: red;'>ไม่พบข้อมูลพืช!</p>");
}

// ดึงข้อมูลการใช้ทางการแพทย์
$medical_use = '';
$result_medical = $conn->query("SELECT medical_use FROM medical_uses WHERE plant_id = $plant_id");
if ($result_medical && $result_medical->num_rows > 0) {
    $medical = $result_medical->fetch_assoc();
    $medical_use = $medical['medical_use'];
}

// ดึงข้อมูลสารประกอบทั้งหมดที่เกี่ยวข้อง
$compounds = [];
$result_compounds = $conn->query("SELECT compounds.* 
                                FROM compounds
                                JOIN plant_compound  ON compounds.compound_id = plant_compound.compound_id 
                                WHERE plant_compound.plant_id = $plant_id");
if ($result_compounds) {
    $compounds = $result_compounds->fetch_all(MYSQLI_ASSOC);
}

// ตรวจสอบการส่งฟอร์มเพื่ออัปเดต
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ข้อมูลพืช
    $common_name = $conn->real_escape_string($_POST['common_name']);
    $scientific_name = $conn->real_escape_string($_POST['scientific_name']);
    $family = $conn->real_escape_string($_POST['family']);
    $description = $conn->real_escape_string($_POST['plant_description']);
    $medical_use = $conn->real_escape_string($_POST['medical_use']);
    $plant_image = $_FILES['plant_image']['name'];
    $plant_image_tmp = $_FILES['plant_image']['tmp_name'];
    $plant_image_error = $_FILES['plant_image']['error'];

    // อัปโหลดไฟล์ภาพพืช
    $upload_dir = "img/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0775, true);
    }
    if (!is_writable($upload_dir)) {
        die("<p style='color: red;'>โฟลเดอร์ $upload_dir ไม่สามารถเขียนได้ กรุณาตั้งค่าสิทธิ์!</p>");
    }

    $plant_image_path = $plant['plant_image'];
    if (!empty($plant_image) && $plant_image_error === UPLOAD_ERR_OK) {
        $plant_image_path = $upload_dir . basename($plant_image);
        if (!move_uploaded_file($plant_image_tmp, $plant_image_path)) {
            echo "<p style='color: red;'>เกิดข้อผิดพลาดในการอัปโหลดไฟล์ภาพพืช: " . $_FILES['plant_image']['error'] . "</p>";
        }
    }

    // อัปเดตข้อมูลพืช
    $stmt_plant = $conn->prepare("UPDATE plants SET common_name = ?, scientific_name = ?, family = ?, plant_description = ?, plant_image = ? WHERE plant_id = ?");
    $stmt_plant->bind_param("sssssi", $common_name, $scientific_name, $family, $description, $plant_image_path, $plant_id);
    $plant_success = $stmt_plant->execute();
    $stmt_plant->close();

    // อัปเดตหรือเพิ่มข้อมูลการใช้ทางการแพทย์
    $stmt_medical = $conn->prepare("INSERT INTO medical_uses (plant_id, medical_use) VALUES (?, ?) ON DUPLICATE KEY UPDATE medical_use = VALUES(medical_use)");
    $stmt_medical->bind_param("is", $plant_id, $medical_use);
    $medical_success = $stmt_medical->execute();
    $stmt_medical->close();

    // จัดการข้อมูลสารประกอบ
    if (isset($_POST['compound_ids'])) {
        $compound_ids = $_POST['compound_ids'];
        foreach ($compound_ids as $index => $cid) {
            $c_name = $conn->real_escape_string($_POST['compound_name'][$index]);
            $c_molecular = $conn->real_escape_string($_POST['molecular_formula'][$index]);
            $c_type = $conn->real_escape_string($_POST['compound_type'][$index]);
            $c_properties = $conn->real_escape_string($_POST['properties'][$index]);
            $c_image = $_FILES['compound_image']['name'][$index] ?? '';
            $c_image_tmp = $_FILES['compound_image']['tmp_name'][$index] ?? '';
            $c_image_error = $_FILES['compound_image']['error'][$index] ?? UPLOAD_ERR_NO_FILE;

            $compound_image_path = isset($compounds[$index]) ? $compounds[$index]['compound_image'] : null;
            if (!empty($c_image) && $c_image_error === UPLOAD_ERR_OK) {
                $compound_image_path = $upload_dir . basename($c_image);
                if (!move_uploaded_file($c_image_tmp, $compound_image_path)) {
                    echo "<p style='color: red;'>เกิดข้อผิดพลาดในการอัปโหลดไฟล์ภาพสารประกอบ: " . $_FILES['compound_image']['error'][$index] . "</p>";
                }
            }

            if ($cid == 'new') {
                $stmt_compound = $conn->prepare("INSERT INTO compounds (compound_name, molecular_formula, compound_type, properties, compound_image) VALUES (?, ?, ?, ?, ?)");
                $stmt_compound->bind_param("sssss", $c_name, $c_molecular, $c_type, $c_properties, $compound_image_path);
                $stmt_compound->execute();
                $new_compound_id = $conn->insert_id;
                $stmt_relation = $conn->prepare("INSERT INTO plant_compound (plant_id, compound_id) VALUES (?, ?)");
                $stmt_relation->bind_param("ii", $plant_id, $new_compound_id);
                $stmt_relation->execute();
                $stmt_relation->close();
            } else {
                $stmt_compound = $conn->prepare("UPDATE compounds SET compound_name = ?, molecular_formula = ?, compound_type = ?, properties = ?, compound_image = ? WHERE compound_id = ?");
                $stmt_compound->bind_param("sssssi", $c_name, $c_molecular, $c_type, $c_properties, $compound_image_path, $cid);
                $stmt_compound->execute();
            }
            $stmt_compound->close();
        }
    }

    if (isset($_POST['delete_compound']) && is_array($_POST['delete_compound'])) {
        foreach ($_POST['delete_compound'] as $delete_id) {
            $stmt_delete = $conn->prepare("DELETE FROM compounds WHERE compound_id = ?");
            $stmt_delete->bind_param("i", $delete_id);
            $stmt_delete->execute();
            $stmt_delete->close();

            $stmt_relation_delete = $conn->prepare("DELETE FROM plant_compound WHERE compound_id = ? AND plant_id = ?");
            $stmt_relation_delete->bind_param("ii", $delete_id, $plant_id);
            $stmt_relation_delete->execute();
            $stmt_relation_delete->close();
        }
    }

    if ($plant_success && $medical_success) {
        echo "<script>
            alert('Update Data Success');
            window.location.href = 'dashbord.php';
        </script>";
    } else {
        echo "<p style='color: red;'>เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data</title>
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
        .form-group button, .form-group input[type="checkbox"] {
            margin-top: 5px;
        }
        .compound-section {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
        }
        .compound-section button {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .compound-section button:hover {
            background-color: #cc0000;
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
        .add-compound {
            background-color: #4CAF50;
            color: white;
            margin-top: 10px;
        }
        .add-compound:hover {
            background-color: #45a049;
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
        <h1>Edit Data</h1>
        <h2>Edit Plant Information</h2>
        <form method="POST" enctype="multipart/form-data" action="">
            <div class="form-group">
                <label for="common_name">Common Name</label>
                <input type="text" id="common_name" name="common_name" value="<?php echo htmlspecialchars($plant['common_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="scientific_name">Scientific Name</label>
                <input type="text" id="scientific_name" name="scientific_name" value="<?php echo htmlspecialchars($plant['scientific_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="family">Family</label>
                <input type="text" id="family" name="family" value="<?php echo htmlspecialchars($plant['family']); ?>" required>
            </div>
            <div class="form-group">
                <label for="plant_description">Description</label>
                <textarea id="plant_description" name="plant_description" rows="4" required><?php echo htmlspecialchars($plant['plant_description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="medical_use">Medical Use</label>
                <input type="text" id="medical_use" name="medical_use" value="<?php echo htmlspecialchars($medical_use); ?>" required>
            </div>
            <div class="form-group">
                <label for="plant_image">Image (leave blank to keep current)</label>
                <input type="file" id="plant_image" name="plant_image" accept="image/*">
                <?php if ($plant['plant_image']) echo "<p>Current Image: <img src='" . htmlspecialchars($plant['plant_image']) . "' width='100'></p>"; ?>
            </div>

            <h2>Edit Compound Information</h2>
            <?php foreach ($compounds as $index => $compound): ?>
                <div class="compound-section">
                    <input type="hidden" name="compound_ids[]" value="<?php echo $compound['compound_id']; ?>">
                    <div class="form-group">
                        <label for="compound_name[<?php echo $index; ?>]">Compound Name</label>
                        <input type="text" id="compound_name[<?php echo $index; ?>]" name="compound_name[<?php echo $index; ?>]" value="<?php echo htmlspecialchars($compound['compound_name']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="molecular_formula[<?php echo $index; ?>]">Molecular Formula</label>
                        <input type="text" id="molecular_formula[<?php echo $index; ?>]" name="molecular_formula[<?php echo $index; ?>]" value="<?php echo htmlspecialchars($compound['molecular_formula']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="compound_type[<?php echo $index; ?>]">Compound Type</label>
                        <input type="text" id="compound_type[<?php echo $index; ?>]" name="compound_type[<?php echo $index; ?>]" value="<?php echo htmlspecialchars($compound['compound_type']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="properties[<?php echo $index; ?>]">Properties</label>
                        <input type="text" id="properties[<?php echo $index; ?>]" name="properties[<?php echo $index; ?>]" value="<?php echo htmlspecialchars($compound['properties']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="compound_image[<?php echo $index; ?>]">Image (leave blank to keep current)</label>
                        <input type="file" id="compound_image[<?php echo $index; ?>]" name="compound_image[<?php echo $index; ?>]" accept="image/*">
                        <?php if ($compound['img_path']) echo "<p>Current Image: <img src='" . htmlspecialchars($compound['compound_image']) . "' width='100'></p>"; ?>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" id="delete_compound[<?php echo $index; ?>]" name="delete_compound[]" value="<?php echo $compound['compound_id']; ?>">
                        <label for="delete_compound[<?php echo $index; ?>]">Delete this compound</label>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- เพิ่มสารประกอบใหม่ -->
            <div class="form-group">
                <button type="button" class="add-compound" onclick="addCompound()">Add New Compound</button>
            </div>

            <div class="form-group">
                <button type="submit">Update Data</button>
            </div>
        </form>
        <?php if (isset($message)) echo $message; ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        <img onclick="window.location.href='index.php'" src="img/Center_of_Excellence1-removebg-preview.png" alt="Logo">
        <span>Center of Excellence in Herbal & Bioactive Compounds | Copyright © 2025</span>
    </div>

    <script>
        let compoundIndex = <?php echo count($compounds); ?>;

        function addCompound() {
            const compoundSection = document.createElement('div');
            compoundSection.className = 'compound-section';
            compoundSection.innerHTML = `
                <input type="hidden" name="compound_ids[]" value="new">
                <div class="form-group">
                    <label for="compound_name[${compoundIndex}]">Compound Name</label>
                    <input type="text" id="compound_name[${compoundIndex}]" name="compound_name[${compoundIndex}]" value="">
                </div>
                <div class="form-group">
                    <label for="molecular_formula[${compoundIndex}]">Molecular Formula</label>
                    <input type="text" id="molecular_formula[${compoundIndex}]" name="molecular_formula[${compoundIndex}]" value="">
                </div>
                <div class="form-group">
                    <label for="compound_type[${compoundIndex}]">Compound Type</label>
                    <input type="text" id="compound_type[${compoundIndex}]" name="compound_type[${compoundIndex}]" value="">
                </div>
                <div class="form-group">
                    <label for="properties[${compoundIndex}]">Properties</label>
                    <input type="text" id="properties[${compoundIndex}]" name="properties[${compoundIndex}]" value="">
                </div>
                <div class="form-group">
                    <label for="compound_image[${compoundIndex}]">Image</label>
                    <input type="file" id="compound_image[${compoundIndex}]" name="compound_image[${compoundIndex}]" accept="image/*">
                </div>
            `;
            document.querySelector('.content').insertBefore(compoundSection, document.querySelector('.add-compound').parentElement);
            compoundIndex++;
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>