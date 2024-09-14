<?php
session_start();
require_once "database.php";
require_once "helpers.php";




if (!isset($_SESSION['user'])) {
    echo "You are not logged in. Please log in first.";
    exit();
}


$sql = "SELECT role FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);



if (!hasPermission('admin')) {
    echo "You don't have permission to access this page.";
    exit();
}



$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #e0f7fa, #80deea); 
        padding: 20px;
        margin: 0;
    }

    .container {
        width: 100%;
        max-width: 1000px;
        margin: auto;
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0px 0px 15px 0px #00000020;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-top: 5px solid #00796b;
        overflow-x: auto; 
    }

    .container:hover {
        transform: scale(1.03);
        box-shadow: 0px 0px 30px 0px #00000040;
    }

    h2 {
        text-align: center;
        color: #00695c;
        margin-bottom: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    table {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
        min-width: 600px; 
    }

    table, th, td {
        border: 1px solid #dee2e6;
    }

    th, td {
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: #e0f2f1;
        color: #00796b;
        font-weight: 600;
    }

    tr:hover {
        background-color: #e0f7fa;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 5px 10px;
        border-radius: 20px;
        text-decoration: none;
        color: white;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .btn-edit {
        background-color: #28a745;
    }

    .btn-edit:hover {
        background-color: #218838;
        transform: translateY(-4px);
    }

    .btn-delete {
        background-color: #dc3545;
    }

    .btn-delete:hover {
        background-color: #c82333;
        transform: translateY(-4px);
    }

    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 15px;
        }

        th, td {
            padding: 10px;
            font-size: 14px;
        }

        .btn {
            padding: 4px 8px;
            font-size: 14px;
        }
    }

    @media (max-width: 576px) {
        h2 {
            font-size: 20px;
        }

        th, td {
            padding: 8px;
            font-size: 12px;
        }

        .btn {
            padding: 3px 6px;
            font-size: 12px;
        }
    }

    @media (max-width: 375px) {
        h2 {
            font-size: 18px;
        }

        th, td {
            padding: 6px;
            font-size: 11px;
        }

        .btn {
            padding: 2px 5px;
            font-size: 11px;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
</style>
</head>
<body>
    <div class="container mt-4">
        <h2>Manage Users</h2>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo $row['address']; ?></td>
                            <td><?php echo $row['role']; ?></td>
                            <td class="action-buttons">
                                <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a>
                                <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </div>
    
    <script>
window.onload = function() {
    if (!localStorage.getItem('welcomeShown')) {
        alert("Welcome, admin! You have access to this page.");
        localStorage.setItem('welcomeShown', true);
    }
};
</script>
</body>
</html>