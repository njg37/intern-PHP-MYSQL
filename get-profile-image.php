<?php
session_start();
require_once "database.php";

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && isset($user['profile_picture']) && !empty($user['profile_picture'])) {
        $image_path = $_SERVER['DOCUMENT_ROOT'] . "/login-register/" . ltrim($user['profile_picture'], '/');
        $image_type = pathinfo($image_path, PATHINFO_EXTENSION);
        header("Content-Type: image/" . $image_type);
        readfile($image_path);
    } else {

        $default_image_path = $_SERVER['DOCUMENT_ROOT'] . "/login-register/images/default_profile_picture.jpg";
        header("Content-Type: image/jpeg");
        readfile($default_image_path);
    }
} else {
    header("HTTP/1.0 404 Not Found");
}
?>