<?php

session_start();


if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}


require_once "database.php";


$id = $_GET['id'];


$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
}


if (isset($_POST['update'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssi", $fullname, $email, $phone, $address, $id);
        mysqli_stmt_execute($stmt);
        header('Location: manage_users.php');
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #e0f7fa, #80deea);
    color: #333;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; 
}

.container {
    max-width: 400px;
    margin: 20px;
    padding: 20px;
    background-color: #ffffff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 12px; 
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-top: 5px solid #00796b;
}

.container:hover {
    transform: scale(1.03);
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
}

h2 {
    text-align: center;
    color: #00695c;
    margin-bottom: 20px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.form-group {
    position: relative;
    margin-bottom: 1.2rem; 
}

.form-control {
    width: 100%;
    padding: 10px 15px; 
    font-size: 0.9rem; 
    color: #333;
    background-color: #e0f2f1;
    border: 2px solid #b2dfdb;
    border-radius: 20px; 
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #00796b;
    background-color: #ffffff;
    box-shadow: 0 0 8px rgba(0, 121, 107, 0.5);
    transform: scale(1.01);
}

.form-control:hover {
    border-color: #00796b;
}

.form-control::placeholder {
    color: #00796b;
}

.form-btn input[type="submit"] {
    width: 100%;
    padding: 10px; 
    font-size: 1rem;
    font-weight: 600;
    color: #ffffff;
    background-color: #00796b;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.form-btn input[type="submit"]:hover {
    background-color: #004d40;
    transform: translateY(-4px);
}

.form-btn input[type="submit"]:active {
    background-color: #00332c;
    transform: translateY(0);
}

a {
    color: #00796b;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

a:hover {
    color: #004d40;
    text-decoration: underline;
}

.alert {
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-size: 1rem;
    transition: opacity 0.3s ease;
}

.alert-danger {
    background-color: #ffebee;
    color: #b71c1c;
    border: 1px solid #ffcdd2;
    border-left: 4px solid #b71c1c;
}

.alert-success {
    background-color: #e8f5e9;
    color: #1b5e20;
    border: 1px solid #c8e6c9;
    border-left: 4px solid #1b5e20;
}
@media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .form-btn input {
                padding: 8px;
            }
        }

        @media (max-width: 576px) {
            h2 {
                font-size: 24px;
            }

            .form-btn input {
                padding: 7px;
                font-size: 14px;
            }
        }


    </style>
</head>
<body>
    <div class="container">
        <h2 class="my-4">Edit User</h2>
        <form method="post">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" class="form-control" name="fullname" value="<?php echo $user['full_name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo $user['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" name="phone" value="<?php echo $user['phone']; ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" name="address" value="<?php echo $user['address']; ?>" required>
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Update" name="update">
            </div>
        </form>
    </div>
</body>
</html>