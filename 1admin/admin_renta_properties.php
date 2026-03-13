<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch rent properties from addrentproperty table
$query = "SELECT * FROM addrentproperty ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Rent Property Listings</title>
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
            word-break: break-word;
        }

        .sidebar p {
            color: #cfd8dc;
            font-size: 14px;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar a, .sidebar .submenu-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            margin-bottom: 15px;
            text-decoration: none;
            color: white;
            border-radius: 8px;
            background-color: #2c2c54;
            transition: background 0.3s ease, transform 0.2s ease;
            font-weight: bold;
            cursor: pointer;
        }

        .sidebar a:hover, .sidebar .submenu-toggle:hover {
            background-color: #1565c0;
            transform: scale(1.05);
        }

        .sidebar a.logout {
            background-color: #2c2c54;
        }

        .sidebar a.logout:hover {
            background-color: #b71c1c !important;
            transform: scale(1.05);
        }

        .submenu {
            display: none;
            flex-direction: column;
            margin-left: 15px;
        }

        .submenu a {
            background-color: #1a1a2f;
            font-size: 14px;
        }

        .submenu a:hover {
            background-color: #1565c0;
        }

        .triangle {
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 8px solid white;
            transition: transform 0.3s ease;
        }

        .triangle.open {
            transform: rotate(180deg);
        }

        .main-content {
            flex-grow: 1;
            background: #1e1e2f;
            padding: 40px;
            overflow-y: auto;
        }

        .main-content h2 {
            font-size: 28px;
            color: #00bfff;
            margin-bottom: 25px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #1e1e3f;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        th {
            background: #2b2b60;
            color: #00bfff;
        }

        .actions {
            display: flex;
            gap: 6px;
        }

        .btn {
            padding: 8px 12px;
            border-radius: 6px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: inline-block;
            transition: background 0.3s ease;
        }

        .btn-view {
            background: #007bff;
        }

        .btn-view:hover {
            background: #0056b3;
        }

        .btn-edit {
            background: #28a745;
        }

        .btn-edit:hover {
            background: #1e7e34;
        }

        .btn-delete {
            background: #dc3545;
        }

        .btn-delete:hover {
            background: #bd2130;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Welcome Admin 👨‍💼</h2>
    <p>Manage the system effectively</p>

    <a href="/jaga/landrecordsys/1admin/admin_users.php">👤 User Details</a>

    <div class="submenu-container">
        <div class="submenu-toggle">
            ➕ Sell/Rent Property
            <div class="triangle"></div>
        </div>
        <div class="submenu">
            <a href="/jaga/landrecordsys/1admin/add_property.php?type=sell">Sell Property</a>
            <a href="/jaga/landrecordsys/1admin/add_renta_property.php?type=rent">Rent Property</a>
        </div>
    </div>

    <div class="submenu-container">
        <div class="submenu-toggle">
            🏠 View Properties
            <div class="triangle"></div>
        </div>
        <div class="submenu">
            <a href="/jaga/landrecordsys/1admin/admin_properties.php?filter=buy">Buy Properties</a>
            <a href="/jaga/landrecordsys/1admin/admin_renta_properties.php?filter=rent">Rent Properties</a>
        </div>
    </div>

    <a href="/jaga/landrecordsys/1admin/admin_analytics.php">📊 Analytics Dashboard</a>
    <a href="/jaga/landrecordsys/1admin/admin_messages.php">📥 MSG</a>
    <a href="admin_logout.php" class="logout">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2>🏠 Rent Property Listings</h2>
    <table>
        <thead>
            <tr>
                <th>Property ID</th>
                <th>Owner Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Property Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['property_id']) ?></td>
                <td><?= htmlspecialchars($row['owner_name']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['property_type']) ?></td>
                <td>
                    <div class="actions">
                        <a href="view_renta_properties.php?id=<?= urlencode($row['property_id']) ?>" class="btn btn-view">View</a>
                        <a href="edit_renta_properties.php?id=<?= urlencode($row['property_id']) ?>" class="btn btn-edit">Edit</a>
                        <a href="delete_renta_properties.php?id=<?= urlencode($row['property_id']) ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this rent property?');">Delete</a>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script>
    const toggles = document.querySelectorAll('.submenu-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            const submenu = toggle.nextElementSibling;
            const triangle = toggle.querySelector('.triangle');
            submenu.style.display = submenu.style.display === 'flex' ? 'none' : 'flex';
            triangle.classList.toggle('open');
        });
    });
</script>

</body>
</html>
