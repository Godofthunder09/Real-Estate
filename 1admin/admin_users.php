<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle delete request
if (isset($_GET['delete_user'])) {
    $delete_id = intval($_GET['delete_user']);
    mysqli_query($con, "DELETE FROM users WHERE id = $delete_id");
    header("Location: admin_users.php"); // Refresh after deletion
    exit();
}

$userQuery = "SELECT * FROM users";
$userResult = mysqli_query($con, $userQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - User Management</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #121212;
            color: #fff;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(to bottom right, #14213d, #1a2a6c);
            padding: 30px 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
            flex-shrink: 0;
        }

        .sidebar h2 {
            color: #9be1ff;
            margin-bottom: 10px;
            font-size: 20px;
            line-height: 1.5;
            text-align: center;
        }

        .sidebar p {
            color: #cfd8dc;
            font-size: 14px;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            margin-bottom: 15px;
            text-decoration: none;
            color: white;
            border-radius: 8px;
            background-color: #2c2c54;
            transition: background 0.3s ease, transform 0.2s ease;
            font-weight: bold;
            text-align: center;
        }

        .sidebar a:hover {
            background-color: #1565c0;
            transform: scale(1.05);
        }

        .sidebar a.logout:hover {
            background-color: #b71c1c !important;
        }

        .main-content {
            flex-grow: 1;
            background: #1e1e2f;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px;
            overflow-y: auto;
        }

        .welcome-box {
            background: #2a2a40;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 150, 255, 0.2);
            width: 100%;
            max-width: 800px;
        }

        .welcome-box h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #76c7f3;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: #1e1e3f;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        th {
            background: #2b2b60;
            color: #00bfff;
        }

        .delete-btn {
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #a71d2a;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Welcome Admin 👨‍💼</h2>
    <p>Manage the system effectively</p>
    <a href="/jaga/landrecordsys/1admin/admin_users.php">👤 User Details</a>
     <a href="/jaga/landrecordsys/1admin/add_property.php">➕ sell-rent Property</a>
    <a href="/jaga/landrecordsys/1admin/admin_properties.php">🏠 View Properties</a>
    <a href="/jaga/landrecordsys/1admin/admin_analytics.php">📊 Analytics Dashboard</a>
    <a href="/jaga/landrecordsys/1admin/admin_messages.php">📥 MSG</a>
    <a href="admin_logout.php" class="logout">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="welcome-box">
        <h2>👥 Registered Users</h2>
        <table>
            <thead>
                <tr>
                    <th>Sr No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sr = 1;
                while ($row = mysqli_fetch_assoc($userResult)) {
                    echo "<tr>
                        <td>" . $sr++ . "</td>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>
                            <a href='admin_users.php?delete_user=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this user?');\">
                                <button class='delete-btn'>Delete</button>
                            </a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
