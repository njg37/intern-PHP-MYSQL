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
