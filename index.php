<?php
session_start();
if(!isset($_SESSION['user'])){
    header('Location: login.php');
    exit();  
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
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

.index-container {
    max-width: 400px;
    margin: 20px auto;
    padding: 20px;
    background-color: #ffffff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    border-top: 5px solid #00796b;
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

.index-container .alert {
    margin-top: -10px;
}

@media (max-width: 768px) {
    .index-container {
        padding: 15px;
    }
}

@media (max-width: 576px) {
    .index-container h2 {
        font-size: 24px;
    }
}

    </style>
<body>
    <div class="container">
        <h1>Welcome to Dashboard</h1>
        <a href= "logout.php" class="btn btn-warning ">Logout</a>
    </div>
</body>
</html>