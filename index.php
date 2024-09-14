<?php
session_start();
require_once "database.php";
require_once "helpers.php";
require_once "weather.php";

if(!isset($_SESSION['user'])){
    header('Location: login.php');
    exit();  
}

$user_id = $_SESSION['user'];


if (!is_int($user_id)) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

$apiKey = ''; 
$city = isset($_POST['city']) ? trim($_POST['city']) : 'London';
$weatherData = getWeather($city, $apiKey);

if (isset($_GET['success']) && $_GET['success'] === 'true') {
    ?>
    <script>
        $(document).ready(function() {
            alert("Your profile has been updated successfully!");
        });
    </script>
    <?php
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

body {
    font-family: 'Poppins', sans-serif; 
    background-color: #e0f7fa; 
    color: #333;
    margin: 0;
    padding: 0;
}

.navbar {
    background-color: #00796b;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.navbar-brand {
    font-size: 1.5em;
    color: white;
}

.navbar-nav .nav-item .nav-link {
    color: white;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #ffffff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    border-top: 5px solid #00796b;
    overflow: hidden;
}

.header {
    position: fixed;
    top: 0;
    right: 0;
    z-index: 1030;
    background-color: #ffffff;
    padding: 10px;
    border-radius: 0 0 0 10px;
}

.header button {
    background-color: transparent;
    border: none;
    font-size: 18px;
    color: #00796b;
    cursor: pointer;
}

.header button:hover {
    color: #005a64;
}

.index-container {
    max-width: 400px;
    margin: 40px auto;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.index-container h2 {
    color: #00695c;
    margin-bottom: 20px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.index-container .form-group {
    margin-bottom: 1.2rem;
}

.index-container .form-control {
    padding: 10px 15px;
    font-size: 0.9rem;
    border-radius: 20px;
    transition: all 0.3s ease;
}

.index-container .form-btn input[type="submit"] {
    margin-top: 15px;
}

.index-container a {
    display: block;
    margin-top: 10px;
    font-weight: 600;
}

.card {
    margin-bottom: 20px;
}

.card-title {
    font-size: 24px;
    font-weight: 600;
}

.card-text {
    font-size: 16px;
}

@media (max-width: 576px) {
    .index-container {
        margin-top: 60px;
    }

    .header {
        position: static;
        margin-bottom: 20px;
    }
}
</style>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">WeatherWatcher </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1>Discover latest updates of Nature.</h1>
                <p>Hello, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
                
                <form method="post" class="mb-4">
                    <div class="form-group">
                        <label for="city">Enter your city:</label>
                        <input type="text" class="form-control" id="city" name="city" placeholder="Enter city name" value="<?php echo htmlspecialchars($city); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Get Weather</button>
                </form>
                
                <?php if ($weatherData && isset($weatherData['temperature'], $weatherData['humidity'], $weatherData['description'])): ?>
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Current Weather in <?php echo htmlspecialchars($city); ?></h5>
                        <p class="card-text">Temperature: <?php echo htmlspecialchars($weatherData['temperature']); ?>°C</p>
                        <p class="card-text">Humidity: <?php echo htmlspecialchars($weatherData['humidity']); ?>%</p>
                        <p class="card-text">Description: <?php echo htmlspecialchars($weatherData['description']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
        </div>
        
        <a href="logout.php" class="btn btn-warning mt-4">Logout</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>