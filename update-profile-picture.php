<?php
session_start();
require_once "database.php";

function resizeImage($filename) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . "/login-register/" . $filename;
    
    $image_info = getimagesize($full_path);
    if ($image_info === false) {
        return ['success' => false, 'message' => 'Unable to determine image type'];
    }
    
    switch ($image_info[2]) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($full_path);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($full_path);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($full_path);
            break;
        default:
            return ['success' => false, 'message' => 'Unsupported image type'];
    }
    
    if ($image === false) {
        return ['success' => false, 'message' => 'Failed to load image'];
    }
    
    $width = imagesx($image);
    $height = imagesy($image);
    
    $max_width = 300; 
    $max_height = 300; 
    
    $new_width = ($width > $max_width) ? $max_width : $width;
    $new_height = ($height > $max_height) ? $max_height : $height;
    
    $temp = imagecreatetruecolor($new_width, $new_height);
    
    if (!imagecopyresampled($temp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height)) {
        imagedestroy($temp);
        imagedestroy($image);
        return ['success' => false, 'message' => 'Failed to resize image'];
    }
    
    if (!imagejpeg($temp, $full_path)) {
        imagedestroy($temp);
        imagedestroy($image);
        return ['success' => false, 'message' => 'Failed to save resized image'];
    }
    
    imagedestroy($temp);
    imagedestroy($image);
    
    return ['success' => true];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["new_profile_picture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


    $check = getimagesize($_FILES["new_profile_picture"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo json_encode(['success' => false, 'message' => 'File is not an image.']);
        exit();
    }


    if ($_FILES["new_profile_picture"]["size"] > 5000000) {
        echo json_encode(['success' => false, 'message' => 'Sorry, your file is too large.']);
        exit();
    }


    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo json_encode(['success' => false, 'message' => 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.']);
        exit();
    }


    if ($uploadOk == 0) {
        echo json_encode(['success' => false, 'message' => 'Sorry, your file was not uploaded.']);
        exit();
    } else {
        if (move_uploaded_file($_FILES["new_profile_picture"]["tmp_name"], $target_file)) {
            $resizeResult = resizeImage($target_file);
            if (!$resizeResult['success']) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Resize failed: ' . $resizeResult['message']
                ]);
                exit();
            }


            $new_image_url = "/login-register/uploads/" . basename($_FILES["new_profile_picture"]["name"]);
            $update_query = "UPDATE users SET profile_picture = '$new_image_url' WHERE id = '$user_id'";
            mysqli_query($conn, $update_query);


            $response = array('success' => true, 'message' => $new_image_url);
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sorry, there was an error uploading your file.']);
        }
    }
}
?>