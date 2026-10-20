<?php
session_start();

require 'db_connect.php';



$result = $conn->query("SELECT * FROM plants LIMIT 2");
$plants = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Center of Excellence in Herbal & Bioactive Compounds</title>
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
            padding: 20px 20px;
            display: flex;
            align-items: center;
            justify-content: left;
            border-bottom: 2px solid #fff;
        }

        .header img {
            position: absolute;
            top: 10;
            left: 20;
            width: 100px;
            margin-right: 20px;
            cursor: pointer;
        }

        .header h1 {
            font-size: 24px;
            color: #ff9800;
            margin: 0;
            font-weight: bold;
            margin-left: 125px;
        }

        .header span {
            font-size: 12px;
            color: #d1d1d1;
            display: block;
            text-align: center;
            margin-top: 2px;
        }

        .nav {
            background-color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .nav .menu a {
            color: #333;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .nav .menu a:hover {
            color: #2e7d32;
        }

        .nav .auth a {
            color: #333;
            text-decoration: none;
            margin-left: 15px;
            font-weight: 600;
            text-transform: uppercase;
            border: 1px solid #ccc;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .nav .auth a:hover {
            background-color: #f0f0f0;
        }

        .main {
            background: url('img/rm218batch5-mynt-43.jpg') no-repeat center center;
            background-size: 2000px;
            color: white;
            text-align: center;
            padding: 40px 20px;
            position: relative;
            min-height: 600px;
        }

        .main h2 {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 10px;
            margin-top: 100px;
            text-transform: uppercase;
        }

        .main p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .search-bar {
            width: 60%;
            max-width: 600px;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 5px 0 0 5px;
            outline: none;
        }

        .search-button {
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            background-color: #fff;
            color: #4caf50;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-button:hover {
            background-color: #e0e0e0;
        }

        .search-options {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 10px;
            border-radius: 5px;
        }

        .search-options label {
            margin: 0 15px;
            font-size: 16px;
            color: white;
        }

        .search-options input[type="checkbox"] {
            margin-right: 5px;
        }

        .options {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .option {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            padding: 10px;
        }

        .option img {
            width: 50px;
            height: 50px;
            filter: brightness(0) invert(1);
        }

        .option p {
            margin-top: 10px;
            font-size: 16px;
            color: white;
            font-weight: 600;
        }

        .herbal-list {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
            background-color: #fff;
        }

        .herbal-list h2 {
            color: #2e7d32;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: left;
        }

        .herbal-items {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .herbal-item {
            width: 48%;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 20px;
        }

        .herbal-item img {
            width: 100%;
            max-width: 150px;
            height: auto;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .herbal-item h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 5px;
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

        @media (max-width: 1200px) {
            .header h1 {
                font-size: 20px;
            }

            .nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav .menu a {
                margin: 5px 0;
            }

            .nav .auth {
                margin-top: 10px;
            }

            .main h2 {
                font-size: 36px;
            }

            .main p {
                font-size: 16px;
            }

            .search-bar {
                width: 70%;
            }

            .herbal-item {
                width: 100%;
            }

            .options {
                flex-direction: column;
                align-items: center;
                gap: 20px;
            }
        }

       
        @media (max-width: 768px) {
            .header h1 {
                font-size: 20px;
            }

            .nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav .menu a {
                margin: 5px 0;
            }

            .nav .auth {
                margin-top: 10px;
            }

            .main h2 {
                font-size: 36px;
            }

            .main p {
                font-size: 16px;
            }

            .search-bar {
                width: 70%;
            }

            .herbal-item {
                width: 100%;
            }

            .options {
                flex-direction: column;
                align-items: center;
                gap: 20px;
            }
        }

        @media (max-width: 480px) {
            .header {
                padding: 10px;
            }

            .header img {
                width: 30px;
            }

            .header h1 {
                font-size: 18px;
            }

            .main h2 {
                font-size: 28px;
            }

            .main p {
                font-size: 14px;
            }

            .search-bar {
                width: 80%;
                padding: 10px;
            }

            .search-button {
                padding: 10px;
            }

            .option img {
                width: 40px;
                height: 40px;
            }

            .option p {
                font-size: 14px;
            }

            .herbal-item {
                padding: 10px;
            }

            .herbal-item img {
                max-width: 120px;
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
        <h1>Center of Excellence in Herbal & Bioactive Compounds</h1>
    </div>

    <!-- Navigation -->
    <div class="nav">
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

    <!-- Main Content -->
    <div class="main">
        <h2>Explore Herbal & Bioactive</h2>
        <p>Quickly search herbal information</p>
        <div class="search-container">
            <input type="text" class="search-bar" id="search" placeholder="Search herbal information...">
            <button class="search-button" onclick="searchPlants()"><img src="img/search-2.png" alt="search" height="20px"></button>
        </div>

        <div class="search-options">
            <label><input type="checkbox" id="search_herbals" name="search_type" value="herbals"> Herbals</label>
            <label><input type="checkbox" id="search_compounds" name="search_type" value="compounds"> Compounds</label>
        </div>

        <div class="options">
            <div class="option" onclick="uploadData()">
                <img src="img/upload.png" alt="Upload">
                <p>Upload Data</p>
            </div>
            <div class="option" onclick="window.location.href='plants.php'">
                <img src="img/icons8-natural-food-96-2.png" alt="Herbal">
                <p>Herbal List</p>
            </div>
            <div class="option" onclick="showCompoundsList()">
                <img src="img/dna-2.png" alt="Compounds">
                <p>Compounds List</p>
            </div>
        </div>
    </div>

    <!-- Interested Herbal List -->
    <div class="herbal-list">
        <h2>Interested Herbal List</h2>
        <div class="herbal-items">
            <?php foreach ($plants as $plant): ?>
                <div class="herbal-item">
                    <a href="plants_details.php?id=<?php echo $plant['plant_id']; ?>">
                        <img src="<?php echo $plant['plant_image']; ?>" alt="<?php echo $plant['common_name']; ?>" width="100">
                    </a>
                    <h3>
                        <a href="plants_details.php?id=<?php echo $plant['plant_id']; ?>" style="text-decoration: none; color: inherit;">
                            <?php echo $plant['common_name']; ?>
                        </a>
                    </h3>
                    <p>Scientific name | <?php echo $plant['scientific_name']; ?></p>
                    <p>Family | <?php echo $plant['family']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
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
            const herbalsChecked = document.getElementById('search_herbals').checked;
            const compoundsChecked = document.getElementById('search_compounds').checked;

            if (herbalsChecked && !compoundsChecked) {
                window.location.href = `plants.php?search=${encodeURIComponent(query)}`;
            } else if (!herbalsChecked && compoundsChecked) {
                window.location.href = `compounds.php?search=${encodeURIComponent(query)}`;
            } else if (herbalsChecked && compoundsChecked) {
                alert('Please select either Herbals or Compounds, not both.');
            } else {
                alert('Please select a search type (Herbals or Compounds).');
            }
        }

        function uploadData() {
            window.location.href = 'upload.php';
        }

        function showHerbalList() {
            window.location.href = 'plants.php';
        }

        function showCompoundsList() {
            window.location.href = 'compounds.php';
        }
    </script>

    <?php
    $conn->close();
    ?>
</body>

</html>