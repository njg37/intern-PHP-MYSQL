<?php

session_start();


if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}


require_once "database.php";


$id = $_GET['id'];


$sql = "DELETE FROM users WHERE id = ?";
$stmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header('Location: manage_users.php');
} else {
    echo "Error deleting record: " . mysqli_error($conn);
}
?>
