<?php

require_once "database.php";
require_once "helpers.php";


session_start();


if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About WeatherWatcher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
</head>
<style>
    body {
    font-family: 'Poppins', sans-serif;
    line-height: 1.6;
    margin: 0;
    
    background-color: #f4f7f8;
    color: #333;
}

.container {
    max-width: 1200px;
    margin: 34px auto;
    background-color: white;
    padding: 30px;
    border-radius: 25px;
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

h1 {
    color: #00796b;
    text-align: center;
    margin-bottom: 40px;
    font-size: 2.5rem;
    font-weight: 600;
    letter-spacing: 2px;
}

.about-content {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}


.left-column {
    background-color: #e0f7fa;
    padding: 60px;
    border-radius: 15px;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}





p {
    margin-bottom: 20px;
    font-size: 1.1rem;
    line-height: 1.6;
}



h2 {
    color: #005a64;
    margin-top: 30px;
    margin-bottom: 15px;
}

@media (max-width: 768px) {
    .about-content {
        flex-direction: column;
    }
    .left-column,
    .right-column {
        width: 100%;
        margin-bottom: 20px;
    }
}


@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.fade-in {
    animation: fadeIn 1s ease-out;
}

@keyframes slideUp {
    from { transform: translateY(50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.slide-up {
    animation: slideUp 0.5s ease-out forwards;
    opacity: 0;
    transform: translateY(50px);
}


.container {
    transition: transform 0.3s ease;
}

.container:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
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
</style>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">WeatherWatcher</a>
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
    <div class="container">


        <div class="about-content">
            <div class="left-column">
                <h2>About Us</h2>
                <p>WeatherWatcher is a cutting-edge weather application designed to provide users with accurate and up-to-date weather information.</p>


            </div>
            </div>
            </div>
</body>

</html>