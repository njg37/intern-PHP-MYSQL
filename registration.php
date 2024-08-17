<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();  // To prevent further execution of the script
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php
        if (isset($_POST['submit'])) {
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $passwordrepeat = $_POST['repeat_password'];
            $phone = $_POST['phone'];  // New field for phone number
            $address = $_POST['address'];  // New field for address

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $errors = array();

            // Check for empty fields
            if (empty($fullname) || empty($email) || empty($password) || empty($passwordrepeat) || empty($phone) || empty($address)) {
                array_push($errors, "All fields must be provided");
            }
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, "Invalid email format");
            }
            // Check password length
            if (strlen($password) < 8) {
                array_push($errors, "Password must be at least 8 characters long");
            }
            // Check if passwords match
            if ($password != $passwordrepeat) {
                array_push($errors, "Passwords do not match");
            }
            // Check if the email already exists in the database
            require_once "database.php";
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);

            if ($rowCount > 0) {
                array_push($errors, "Email already exists");
            }

            // Display errors if any
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-danger'>" . $error . "</div>";
                }
            } else {
                // Insert user details into the database
                $sql = "INSERT INTO users (full_name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                if ($prepareStmt) {
                    mysqli_stmt_bind_param($stmt, "sssss", $fullname, $email, $passwordHash, $phone, $address);
                    mysqli_stmt_execute($stmt);
                    echo "<div class='alert alert-success'>Registration successful</div>";
                } else {
                    echo "<div class='alert alert-danger'>Failed to register</div>";
                }
            }
        }
        ?>
        <form method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password">
            </div>
            <div class="form-group">  <!-- New field for phone number -->
                <input type="text" class="form-control" name="phone" placeholder="Phone Number">
            </div>
            <div class="form-group">  <!-- New field for address -->
                <textarea class="form-control" name="address" placeholder="Address"></textarea>
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div><p>Already registered? <a href="login.php">Login Here</a></p></div>
    </div>
</body>
</html>
