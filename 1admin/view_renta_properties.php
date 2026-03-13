<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "No property ID provided.";
    exit();
}

$property_id = mysqli_real_escape_string($con, $_GET['id']);
$query = "SELECT * FROM addrentproperty WHERE property_id = '$property_id' LIMIT 1";
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) == 0) {
    echo "Property not found.";
    exit();
}

$property = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Rent Property - Admin</title>
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

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(to bottom right, #14213d, #1a2a6c);
            padding: 30px 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.3);
            flex-shrink: 0;
        }

        .sidebar h2 {
            color: #9be1ff;
            margin-bottom: 10px;
            font-size: 20px;
            text-align: center;
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

        /* Main Content */
        .main-content {
            flex-grow: 1;
            background: #1e1e2f;
            padding: 40px;
            overflow-y: auto;
        }

        h2 {
            font-size: 28px;
            color: #00bfff;
            margin-bottom: 20px;
        }

        .property-details {
            background: #2a2a40;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,150,255,0.1);
            max-width: 800px;
        }

        .property-details p {
            margin: 10px 0;
            font-size: 16px;
        }

        .property-details span {
            font-weight: bold;
            color: #76c7f3;
        }

        .property-image {
            margin-top: 20px;
        }

        .property-image img {
            max-width: 100%;
            border-radius: 8px;
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s ease;
        }

        .back-link:hover {
            background: #0056b3;
        }

        .image-slider {
            position: relative;
            width: 100%;
            overflow: hidden;
            margin-top: 20px;
        }

        .slider-wrapper {
            display: flex;
            gap: 20px;
            transition: transform 0.5s ease;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding-bottom: 10px;
        }

        .slider-wrapper img {
            height: 200px;
            max-width: 300px;
            border-radius: 8px;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.4);
            object-fit: cover;
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
    <h2>📋 View Rent Property Details</h2>
    <div class="property-details">
        <p><span>Property ID:</span> <?= htmlspecialchars($property['property_id']) ?></p>
        <p><span>Owner Name:</span> <?= htmlspecialchars($property['owner_name']) ?></p>
        <p><span>Phone:</span> <?= htmlspecialchars($property['phone']) ?></p>
        <p><span>Email:</span> <?= htmlspecialchars($property['email']) ?></p>
        <p><span>Property Type:</span> <?= htmlspecialchars($property['property_type']) ?></p>
        <p><span>Location:</span> <?= htmlspecialchars($property['location']) ?></p>
        <p><span>Land Area (sq ft):</span> <?= htmlspecialchars($property['land_area']) ?></p>
        <p><span>Rent Price:</span> <?= htmlspecialchars($property['rent_price']) ?></p>
        <p><span>Security Deposit:</span> <?= htmlspecialchars($property['security_deposit']) ?></p>
        <p><span>Furnishing Status:</span> <?= htmlspecialchars($property['furnishing_status']) ?></p>
        <p><span>Availability Date:</span> <?= htmlspecialchars($property['availability_date']) ?></p>
        <p><span>Rental Duration:</span> <?= htmlspecialchars($property['rental_duration']) ?></p>
        <p><span>Date Added:</span> <?= htmlspecialchars($property['created_at']) ?></p>

        <div class="property-image">
            <span>Images:</span><br>
            <?php
            if (!empty($property['images'])):
                $imageFiles = explode(',', $property['images']);
                $validImages = array_filter(array_map('trim', $imageFiles));
                if (!empty($validImages)):
            ?>
            <div class="image-slider">
                <div class="slider-wrapper">
                    <?php foreach ($validImages as $img):
                        $imgPath = '/jaga/landrecordsys/u/' . basename($img);
                    ?>
                        <img src="<?= htmlspecialchars($imgPath) ?>" alt="Property Image">
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: echo "<p>No valid images available.</p>"; endif;
            else: echo "<p>No images available.</p>"; endif; ?>
        </div>
    </div>

    <a class="back-link" href="admin_renta_properties.php">← Back to Rent Listings</a>
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
