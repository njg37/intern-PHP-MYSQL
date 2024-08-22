<?php
session_start();
require_once "database.php";  


if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $errors = [];


    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }


    if (empty($password)) {
        $errors[] = "Password is required.";
    }

 
    if (empty($errors)) {
        try {
           
            $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($userId, $hashedPassword);
                $stmt->fetch();

              
                if (password_verify($password, $hashedPassword)) {
                 
                    $_SESSION['user'] = $userId;
                    header('Location: index.php');
                    exit();
                } else {
                    $errors[] = "Invalid email or password.";
                }
            } else {
                $errors[] = "No account found with that email.";
            }

            $stmt->close();
        } catch (Exception $e) {
            
            $errors[] = "An error occurred during login. Please try again later.";
            error_log($e->getMessage()); 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script>
        function validateForm() {
            let email = document.forms["loginForm"]["email"].value;
            let password = document.forms["loginForm"]["password"].value;

            let errors = [];

            
            if (email == "") {
                errors.push("Email must be filled out");
            } else if (!/\S+@\S+\.\S+/.test(email)) {
                errors.push("Invalid email format");
            }
            if (password == "") {
                errors.push("Password must be filled out");
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
        <h2>Login</h2>
        <form name="loginForm" method="post" onsubmit="return validateForm()">
            <div class="form-group mb-3">
                <input type="email" class="form-control" name="email" placeholder="Email">
            </div>
            <div class="form-group mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <div class="form-btn mb-3">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }
        ?>
        <div><p>Don't have an account? <a href="registration.php">Register Here</a></p></div>
    </div>
</body>
</html>
