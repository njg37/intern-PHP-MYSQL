<?php
// helpers.php

require_once "database.php";


function uploadProfilePicture($user_id) {
    $target_dir = __DIR__ . "/uploads/"; // Use absolute path
     // Check if upload directory exists and is writable
     if (!is_dir($target_dir)) {
        throw new Exception("Upload directory does not exist.");
    } elseif (!is_writable($target_dir)) {
        throw new Exception("Upload directory is not writable. Please check permissions on " . $target_dir);
    }
    $target_file = $target_dir . basename($_FILES["new_profile_picture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo(basename($_FILES["new_profile_picture"]["name"]), PATHINFO_EXTENSION));

    // Check if upload directory exists and is writable
    if (!is_dir($target_dir) || !is_writable($target_dir)) {
        throw new Exception("Upload directory is not writable.");
    }

    // Check if file is uploaded
    if ($_FILES['new_profile_picture']['tmp_name'] === '') {
        throw new Exception("No file selected.");
    }

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["new_profile_picture"]["tmp_name"]);
    if ($check === false) {
        throw new Exception("File is not an image.");
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        throw new Exception("Sorry, file already exists.");
    }

    // Check file size
    if ($_FILES["new_profile_picture"]["size"] > 500000) {
        throw new Exception("Sorry, your file is too large.");
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        throw new Exception("Sorry, only JPG, PNG, JPEG & GIF files are allowed.");
    }

    // Add a unique identifier to the filename
    $unique_id = uniqid();
    $target_file = $target_dir . $unique_id . '_' . basename($_FILES["new_profile_picture"]["name"]);

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        throw new Exception("Sorry, your file was not uploaded.");
    } else {
        if (move_uploaded_file($_FILES["new_profile_picture"]["tmp_name"], $target_file)) {
            // Update user profile picture in database
            global $conn;
            $query = "UPDATE users SET profile_picture = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $target_file, $user_id);
            if ($stmt->execute()) {
                // Return a success message
                echo json_encode(['success' => true, 'message' => $target_file]);
            } else {
                throw new Exception("Failed to update profile picture in database: " . $conn->error);
            }
        } else {
            throw new Exception("Sorry, there was an error uploading your file.");
        }
    }
}

