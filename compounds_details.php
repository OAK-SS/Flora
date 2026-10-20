<?php
session_start();

require 'db_connect.php';


$compound_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


$compound_result = $conn->query("SELECT * FROM compounds WHERE compound_id = $compound_id");
$compound = $compound_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $compound['compound_name']; ?></title>
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
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .content h1 {
            font-size: 36px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }
        .detail-item {
            background-color: #fff;
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }
        .detail-item label {
            width: 150px;
            font-weight: bold;
            color: #555;
        }
        .detail-item div {
            flex: 1;
        }
        .detail-item img {
            width: 200px;
            height: auto;
            border-radius: 5px;
            margin-right: 20px;
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
            .content h1 {
                font-size: 28px;
            }
            .detail-item {
                flex-direction: column;
                text-align: center;
            }
            .detail-item label {
                width: 100%;
                margin-bottom: 5px;
            }
            .detail-item img {
                margin-right: 0;
                margin-bottom: 10px;
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
            .detail-item img {
                max-width: 150px;
            }
            .detail-item label {
                font-size: 14px;
            }
            .detail-item div {
                font-size: 14px;
            }
            .footer .content {
                font-size: 12px;
            }
            .footer img {
                width: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <img src="img/Center_of_Excellence1-removebg-preview.png" alt="Logo">
        <div class="menu">
            <a href="index.php">HOME</a>
            <a href="plants.php">PLANTS</a>
            <a href="compounds.php">COMPOUNDS</a>
            <a href="upload.php">UPLOAD</a>
            <a href="edit_data.php">EDIT</a>
        </div>
        <div class="auth <?php echo isset($_SESSION['loggedin']) && $_SESSION['loggedin'] ? 'logged-in' : ''; ?>">
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
        <h1><?php echo $compound['compound_name']; ?></h1>
        <div class="detail-item">
            <label>Structure</label>
            <div><img src="<?php echo $compound['compound_image'];?>" alt="Structure of <?php echo $compound['compound_name']; ?>"></div>
        </div>
        <div class="detail-item">
            <label>Molecular Formula</label>
            <div><?php echo $compound['molecular_formula'] ? $compound['molecular_formula'] : 'N/A'; ?></div>
        </div>
        <div class="detail-item">
            <label>Compound Type</label>
            <div><?php echo $compound['compound_type'] ? $compound['compound_type'] : 'N/A'; ?></div>
        </div>
        <div class="detail-item">
            <label>Properties</label>
            <div><?php echo $compound['properties'] ? $compound['properties'] : 'N/A'; ?></div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
            <img onclick="window.location.href='index.php'" src="img/Center_of_Excellence1-removebg-preview.png" alt="Logo">
            <span>Center of Excellence in Herbal & Bioactive Compounds | Copyright © 2025</span>
        </div>

    <?php
    $conn->close();
    ?>
</body>
</html>