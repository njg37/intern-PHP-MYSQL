<?php
session_start();
require_once "database.php";  


if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    
    $errors = [];

   
    if (empty($fullname)) {
        $errors[] = "Full Name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

   
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if ($password !== $repeat_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        $errors[] = "Invalid phone number format.";
    }

    
    if (empty($address)) {
        $errors[] = "Address is required.";
    }

  
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "Email already registered.";
    }
    $stmt->close();

 
    if (empty($errors)) {
        try {
            
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $fullname, $email, $passwordHash, $phone, $address);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Registration successful!</div>";
            } else {
                throw new Exception("Database error: " . $stmt->error);
            }

            $stmt->close();
        } catch (Exception $e) {
            
            error_log($e->getMessage(), 3, "errors.log");
            echo "<div class='alert alert-danger'>An error occurred during registration. Please try again later.</div>";
        }
    } else {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script>
        function validateForm() {
            let fullname = document.forms["registrationForm"]["fullname"].value;
            let email = document.forms["registrationForm"]["email"].value;
            let password = document.forms["registrationForm"]["password"].value;
            let repeat_password = document.forms["registrationForm"]["repeat_password"].value;
            let phone = document.forms["registrationForm"]["phone"].value;
            let address = document.forms["registrationForm"]["address"].value;

            let errors = [];

            
            if (fullname == "") {
                errors.push("Full Name must be filled out");
            }
            if (email == "") {
                errors.push("Email must be filled out");
            } else if (!/\S+@\S+\.\S+/.test(email)) {
                errors.push("Invalid email format");
            }
            if (password.length < 8) {
                errors.push("Password must be at least 8 characters long");
            }
            if (password !== repeat_password) {
                errors.push("Passwords do not match");
            }
            if (phone == "" || !/^[0-9]{10,15}$/.test(phone)) {
                errors.push("Invalid phone number format");
            }
            if (address == "") {
                errors.push("Address must be filled out");
            }

            if (errors.length > 0) {
                alert(errors.join("\n"));
                return false;
            }
        }
    </script>
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
    background-color: #e8f5e9;
    color: #1b5e20;
    border: 1px solid #c8e6c9;
    border-left: 4px solid #1b5e20;
    opacity: 0;
    transform: translateY(-10px);
    animation: fadeInDown 0.6s forwards ease-in-out;
}

@keyframes fadeInDown {
    0% {
        opacity: 0;
        transform: translateY(-10px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-danger {
    background-color: #ffebee;
    color: #b71c1c;
    border: 1px solid #ffcdd2;
    border-left: 4px solid #b71c1c;
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
        <h2>Register</h2>
        <form name="registrationForm" method="post" onsubmit="return validateForm()">
            <div class="form-group mb-3">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name">
            </div>
            <div class="form-group mb-3">
                <input type="email" class="form-control" name="email" placeholder="Email">
            </div>
            <div class="form-group mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <div class="form-group mb-3">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password">
            </div>
            <div class="form-group mb-3">
                <input type="text" class="form-control" name="phone" placeholder="Phone Number">
            </div>
            <div class="form-group mb-3">
                <input type="text" class="form-control" name="address" placeholder="Address">
            </div>
            <div class="form-btn mb-3">
                <input type="submit" class="btn btn-primary" value="Register">
            </div>
        </form>
        <div><p>Already registered? <a href="login.php">Login Here</a></p></div>
    </div>
</body>
</html>

