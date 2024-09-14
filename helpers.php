<?php


require_once "database.php";


function uploadProfilePicture($user_id) {
    $target_dir = __DIR__ . "/uploads/"; 

     if (!is_dir($target_dir)) {
        throw new Exception("Upload directory does not exist.");
    } elseif (!is_writable($target_dir)) {
        throw new Exception("Upload directory is not writable. Please check permissions on " . $target_dir);
    }
    $target_file = $target_dir . basename($_FILES["new_profile_picture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo(basename($_FILES["new_profile_picture"]["name"]), PATHINFO_EXTENSION));


    if (!is_dir($target_dir) || !is_writable($target_dir)) {
        throw new Exception("Upload directory is not writable.");
    }


    if ($_FILES['new_profile_picture']['tmp_name'] === '') {
        throw new Exception("No file selected.");
    }

 
    $check = getimagesize($_FILES["new_profile_picture"]["tmp_name"]);
    if ($check === false) {
        throw new Exception("File is not an image.");
    }


    if (file_exists($target_file)) {
        throw new Exception("Sorry, file already exists.");
    }


    if ($_FILES["new_profile_picture"]["size"] > 500000) {
        throw new Exception("Sorry, your file is too large.");
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        throw new Exception("Sorry, only JPG, PNG, JPEG & GIF files are allowed.");
    }


    $unique_id = uniqid();
    $target_file = $target_dir . $unique_id . '_' . basename($_FILES["new_profile_picture"]["name"]);


    if ($uploadOk == 0) {
        throw new Exception("Sorry, your file was not uploaded.");
    } else {
        if (move_uploaded_file($_FILES["new_profile_picture"]["tmp_name"], $target_file)) {

            global $conn;
            $query = "UPDATE users SET profile_picture = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $target_file, $user_id);
            if ($stmt->execute()) {

                echo json_encode(['success' => true, 'message' => $target_file]);
            } else {
                throw new Exception("Failed to update profile picture in database: " . $conn->error);
            }
        } else {
            throw new Exception("Sorry, there was an error uploading your file.");
        }
    }
}
function hasPermission($permission) {
    global $conn;
    
    try {
        $query = "SELECT COUNT(*) as count FROM users WHERE id = ? AND LOWER(role) = ?";
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            throw new Exception("MySQL statement preparation failed");
        }
        

        $lower_permission = strtolower($permission);
        
        mysqli_stmt_bind_param($stmt, "is", $_SESSION['user'], $lower_permission);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("MySQL statement execution failed");
        }
        mysqli_stmt_store_result($stmt);
        mysqli_stmt_bind_result($stmt, $count);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        return (bool)$count;
    } catch (Exception $e) {
        error_log("Error checking permission: " . $e->getMessage());
        return false;
    }
}