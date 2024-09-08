<?php
session_start();
require_once "database.php";
require_once "helpers.php";

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form submission
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // File is an image
            $uploadOk = 1;
        } else {
            echo json_encode(['success' => false, 'message' => 'File is not an image.']);
            exit();
        }
    }

    // Check if $uploadOk is 1, if so save the file
    if ($uploadOk == 0) {
        echo json_encode(['success' => false, 'message' => 'Sorry, your file was not uploaded.']);
    } else {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            echo "The file " . basename($_FILES["image"]["name"]) . " has been uploaded.";
            
            // Resize the image
            resizeImage($target_file);
            
            // Update user profile with new image URL
            $new_image_url = "/uploads/" . basename($_FILES["image"]["name"]);
            $update_query = "UPDATE users SET profile_picture = '$new_image_url' WHERE id = '" . $_SESSION['user'] . "'";
            mysqli_query($conn, $update_query);
            
            // After successful upload and database update
            $response = array('success' => true, 'message' => $new_image_url);
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sorry, there was an error uploading your file.']);
            exit();
        }
    }
}

function resizeImage($filename) {
    if (!function_exists('imagecreatefromjpeg')) {
        echo "GD library is not enabled. Please enable it in your php.ini file.";
        return;
    }

    $full_path = $_SERVER['DOCUMENT_ROOT'] . "/login-register/" . $filename;
    list($width, $height) = getimagesize($full_path);
    
    $new_width = 300;
    $new_height = ($height / $width) * $new_width;
    
    $image = imagecreatefromjpeg($filename);
    $temp = imagecreatetruecolor($new_width, $new_height);
    
    imagecopyresampled($temp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    imagejpeg($temp, $filename);
    imagedestroy($temp);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $errors = [];

    if (empty($fullname)) {
        $errors[] = "Full Name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        $errors[] = "Invalid phone number format.";
    }

    if (empty($address)) {
        $errors[] = "Address is required.";
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $fullname, $email, $phone, $address, $user_id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Profile updated successfully!</div>";

                // Check if a profile picture was uploaded
                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['size'] > 0) {
                    uploadProfilePicture($user_id);
                }
            } else {
                throw new Exception("Database error: " . $stmt->error);
            }
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>An error occurred while updating your profile.</div>";
        }
    } else {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
}

// Fetch user data
global $conn;
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$has_profile_picture = isset($user['profile_picture']) && !empty($user['profile_picture']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

        /* .profile-picture-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .edit-button-container {
            text-align: center;
            margin-top: 20px;
        }

        .edit-button {
            display: inline-block;
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-icon-link {
            font-size: 24px;
            color: #007bff;
            transition: all 0.3s ease;
        }

        .edit-icon-link:hover {
            color: #0056b3;
            transform: scale(1.2);
        } */

        .modal-backdrop {
            z-index: 1030 !important;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: auto;
        }

        .button-container button,
        .button-container a {
            flex-grow: 1;
            max-width: 200px;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .back-button {
            background-color: transparent;
            color: #00796b;
            border: 2px solid #00796b;

        }

        .update-profile-button {
            background-color: transparent;
            color: #00796b;
            border: 2px solid #00796b;
        }

        .back-button:hover,
        .update-profile-button:hover {
            transform: scale(1.05);
            background-color: #00796b;
            color: white;
            text-decoration: none;
        }



        .profile-picture-container {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            margin: 20px auto;
            z-index: 1;
        }

        .profile-picture {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            position: static;
        }

        .edit-button {
            position: absolute;
            top: 78px;
            right: 10px;
            background-color: transparent;
            color: #00796b;
            border: none;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 3;
        }

        .edit-button:hover {
            transform: scale(1.2);
            color: #00796b;
        }

        .preview-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 20px;
}
.preview-image.profile-picture-modal {
    max-width: 100%;
    height: auto;
    width: 300px; /* Adjust this value based on your modal size */
    object-fit: cover;
    border-radius: 50%; /* Optional: makes the image circular */
}
.profile-picture-container img {
    width: 100px; /* Adjust as needed */
    height: auto;
    object-fit: cover;
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

<body>

<div class="container">
        <h2>My Profile</h2>
        <form method="post" action="/login-register/profile.php">
            <div class="mt-4">
                <h4>Current Profile Picture:</h4>

                <div class="profile-picture-container">
                <img src="/login-register/get-profile-image.php?id=<?php echo $_SESSION['user']; ?>" alt="Profile Picture">
                    <?php if ($has_profile_picture): ?>
                        <button type="button" class="edit-button" data-bs-toggle="modal" data-bs-target="#editProfilePictureModal">
                            <i class="fas fa-camera"></i>
                        </button>
                    <?php endif; ?>
                    <a href="edit-profile.php" class="edit-icon-link"><i class="fas fa-edit"></i></a>
                </div>
            </div>

            <!-- <div class="mb-3">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" class="form-control" id="profile_picture" name="profile_picture">
            </div> -->

            <div class="mb-3">
                <label for="fullname">Full Name:</label>
                <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="phone">Phone Number:</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="address">Address:</label>
                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>

            <!-- <div class="row">
            <div class="col-md-12 mb-4">
                <a href="index.php" class="btn btn-outline-primary back-button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                <button type="submit" class="btn btn-primary ms-auto">Update Profile</button>
            </div>
        </div> -->
        </form>
        <div class="button-container">
            <a href="/login-register/index.php" class="back-button"><i class="fas fa-arrow-left"></i> Back</a>
            <button type="submit" class="update-profile-button"><i class="fas fa-user-edit"></i> Update</button>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editProfilePictureModal" tabindex="-1" aria-labelledby="editProfilePictureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfilePictureModalLabel">Edit Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Current profile picture:</p>
                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Current Profile Picture" class="mb-3 preview-image profile-picture-modal">
                
                <form action="/login-register/update-profile-picture.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="new_profile_picture" accept="image/*" id="profile-picture-input">
                    <button type="submit" class="btn btn-primary mt-3">Upload New Picture</button>
                </form>
            </div>
        </div>
    </div>
</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    var editIconLink = document.querySelector('.edit-icon-link');
    var profilePictureInput = document.getElementById('profile-picture-input');
    var previewImage = $('.preview-image');

    if (editIconLink) {
        editIconLink.addEventListener('click', function(event) {
            event.preventDefault();
            $('#editProfilePictureModal').modal('show');
        });
    }

    profilePictureInput.addEventListener('change', function() {
        var reader = new FileReader();
        reader.onload = function(e) {
            previewImage.attr('src', e.target.result);
        }
        reader.readAsDataURL(profilePictureInput.files[0]);
    });

// Inside the $(document).ready() function
$('form').on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    
    $.ajax({
        url: "/login-register/update-profile-picture.php",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            try {
                var responseData = JSON.parse(response);
                if (responseData.success) {
                    console.log('Profile picture uploaded successfully');
                    
                    // Update the profile picture display
                    var profilePictureContainer = $('.profile-picture-container');
                    profilePictureContainer.html('<img src="' + responseData.message + '" alt="Profile Picture" style="object-fit: cover;">');
                    $('#editProfilePictureModal').modal('hide'); // Close the modal
                } else {
                    alert(responseData.message);
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
                console.log('Raw response:', response);
                alert('An error occurred while uploading the profile picture.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error uploading profile picture:', error);
            alert('Error uploading profile picture.');
        }
    });
});
});
</script>

</body>

</html>
