<?php
session_start(); 
require 'db_connect.php';


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// ดึงข้อมูลพืชทั้งหมดพร้อมการใช้ทางการแพทย์และสารประกอบ
$plants = [];
$result = $conn->query("SELECT plants.*, 
                               GROUP_CONCAT(DISTINCT medical_uses.medical_use SEPARATOR ', ') AS medical_uses, 
                               GROUP_CONCAT(DISTINCT compounds.compound_name SEPARATOR ', ') AS compounds 
                        FROM plants
                        LEFT JOIN medical_uses ON plants.plant_id = medical_uses.plant_id 
                        LEFT JOIN plant_compound ON plants.plant_id = plant_compound.plant_id 
                        LEFT JOIN compounds ON plant_compound.compound_id = compounds.compound_id 
                        GROUP BY plants.plant_id");
if ($result) {
    $plants = $result->fetch_all(MYSQLI_ASSOC);
}

// ตรวจสอบการลบข้อมูล
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $plant_id = (int)$_GET['id'];
    $stmt_delete_plant = $conn->prepare("DELETE FROM plants WHERE plant_id = ?");
    $stmt_delete_plant->bind_param("i", $plant_id);
    $plant_success = $stmt_delete_plant->execute();
    if ($plant_success) {
        $conn->query("DELETE FROM medical_uses WHERE plant_id = $plant_id");
        $conn->query("DELETE FROM plant_compound WHERE plant_id = $plant_id");
        $conn->query("DELETE FROM compounds WHERE compound_id IN (SELECT compound_id FROM plant_compound WHERE plant_id = $plant_id)");
    }
    $stmt_delete_plant->close();
    header("Location: dashbord.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            max-width: 1200px;
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
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #2e7d32;
            color: white;
        }
        td img {
            max-width: 100px;
            max-height: 100px;
        }
        .action-buttons button {
            padding: 5px 10px;
            margin-right: 5px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .action-buttons .edit {
            background-color: #4CAF50;
            color: white;
        }
        .action-buttons .edit:hover {
            background-color: #45a049;
        }
        .action-buttons .delete {
            background-color: #f44336;
            color: white;
        }
        .action-buttons .delete:hover {
            background-color: #da190b;
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
            th, td {
                font-size: 14px;
                padding: 8px;
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
            .table-container {
                overflow-x: scroll;
            }
            th, td {
                font-size: 12px;
                padding: 6px;
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
        <h1>Dashboard</h1>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Common Name</th>
                        <th>Scientific Name</th>
                        <th>Family</th>
                        <th>Description</th>
                        <th>Medical Use</th>
                        <th>Compounds</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plants as $plant): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($plant['common_name']); ?></td>
                            <td><?php echo htmlspecialchars($plant['scientific_name']); ?></td>
                            <td><?php echo htmlspecialchars($plant['family']); ?></td>
                            <td><?php echo htmlspecialchars(substr($plant['plant_description'], 0, 50)) . (strlen($plant['plant_description']) > 50 ? '...' : ''); ?></td>
                            <td><?php echo htmlspecialchars($plant['medical_uses'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($plant['compounds'] ?? 'N/A'); ?></td>
                            <td><img src="<?php echo htmlspecialchars($plant['plant_image'] ?? 'https://via.placeholder.com/100'); ?>" alt="<?php echo htmlspecialchars($plant['common_name']); ?>"></td>
                            <td class="action-buttons">
                                <button class="edit" onclick="window.location.href='edit_data.php?id=<?php echo $plant['plant_id']; ?>'">Edit</button>
                                <button class="delete" onclick="if(confirm('คุณแน่ใจหรือไม่ที่จะลบ?')) window.location.href='dashbord.php?action=delete&id=<?php echo $plant['plant_id']; ?>'">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
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