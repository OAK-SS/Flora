<?php
session_start();

require 'db_connect.php';

$result = $conn->query("SELECT * FROM plants");
$plants = $result->fetch_all(MYSQLI_ASSOC);

// ฟังก์ชันค้นหา
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
if ($search_query) {
    $result = $conn->query("SELECT * FROM plants WHERE common_name LIKE '%$search_query%' OR scientific_name LIKE '%$search_query%' OR family LIKE '%$search_query%'");
    $plants = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Herbs List</title>
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
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .content h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        .search-container {
            display: flex;
            margin-bottom: 20px;
        }

        .search-bar {
            width: 100%;
            max-width: 600px;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-right: none;
            border-radius: 5px 0 0 5px;
            outline: none;
        }

        .search-button {
            padding: 12px 20px;
            font-size: 16px;
            border: 1px solid #ccc;
            background-color: #fff;
            color: #666;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-button:hover {
            background-color: #f0f0f0;
        }

        .herbal-list {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 20px;
        }

        .herbal-item {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            width: 100%;
            /* max-width: 600px; */
        }

        .herbal-item img {
            width: 150px;
            height: auto;
            border-radius: 5px;
            margin-right: 20px;
        }

        .herbal-item h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 3px;
        }

        .herbal-item p {
            font-size: 14px;
            color: #666;
            margin: 2px 0;
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

            .content h1 {
                font-size: 24px;
            }

            .search-bar {
                width: 80%;
            }

            .herbal-item {
                padding: 10px;
            }

            .herbal-item img {
                max-width: 120px;
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

            .search-bar {
                width: 100%;
                padding: 10px;
            }

            .search-button {
                padding: 10px;
            }

            .herbal-item img {
                max-width: 100px;
            }

            .herbal-item h3 {
                font-size: 16px;
            }

            .herbal-item p {
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
        <h1>Herbs List</h1>
        <div class="search-container">
            <input type="text" class="search-bar" id="search" placeholder="Search" value="<?php echo htmlspecialchars($search_query); ?>">
            <button class="search-button" onclick="searchPlants()"><img src="img/search-2.png" alt="search" height="20px"></button>
        </div>

        <div class="herbal-list">
            <?php foreach ($plants as $plant): ?>
                <div class="herbal-item">
                    <a href="plants_details.php?id=<?php echo $plant['plant_id']; ?>">
                        <img src="<?php echo $plant['plant_image']; ?>" alt="<?php echo $plant['common_name']; ?>" width="100">
                    </a>
                    <div class="herbal-details">
                        <h3>
                            <a href="plants_details.php?id=<?php echo $plant['plant_id']; ?>" style="text-decoration: none; color: inherit;">
                                <?php echo $plant['common_name']; ?>
                            </a>
                        </h3>
                        <p><strong>Scientific name:</strong> <?php echo htmlspecialchars($plant['scientific_name']); ?></p>
                        <p><strong>Family:</strong> <?php echo htmlspecialchars($plant['family']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Footer -->
        <div class="footer">
            <img onclick="window.location.href='index.php'" src="img/Center_of_Excellence1-removebg-preview.png" alt="Logo">
            <span>Center of Excellence in Herbal & Bioactive Compounds | Copyright © 2025</span>
        </div>

        <!-- JavaScript -->
        <script>
            function searchPlants() {
                const query = document.getElementById('search').value;
                window.location.href = `plants.php?search=${encodeURIComponent(query)}`;
            }
        </script>

        <?php
        $conn->close();
        ?>
</body>

</html>