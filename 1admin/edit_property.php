<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Property ID is missing.";
    exit();
}

$property_id = $_GET['id'];

// Fetch property data
$query = "SELECT * FROM addproperty WHERE property_id = '$property_id'";
$result = mysqli_query($con, $query);
$property = mysqli_fetch_assoc($result);

if (!$property) {
    echo "Property not found.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $property_type = $_POST['property_type'];
    $location = $_POST['location'];
    $land_area = $_POST['land_area'];
    $price_range = $_POST['price_range'];

    // Handle image upload
    if (!empty($_FILES['new_image']['name'])) {
        $upload_dir = "/jaga/landrecordsys/rent_upload/"; // Updated folder
        $file_name = uniqid() . "_" . basename($_FILES["new_image"]["name"]);
        $target_path = $_SERVER['DOCUMENT_ROOT'] . $upload_dir . $file_name;

        if (move_uploaded_file($_FILES["new_image"]["tmp_name"], $target_path)) {
            $images = $upload_dir . $file_name;
        } else {
            echo "Error uploading image.";
            exit();
        }
    } else {
        $images = $property['images'];
    }

    $update_query = "UPDATE addproperty 
                     SET phone='$phone', 
                         property_type='$property_type', 
                         location='$location', 
                         land_area='$land_area', 
                         price_range='$price_range', 
                         images='$images' 
                     WHERE property_id='$property_id'";

    if (mysqli_query($con, $update_query)) {
        echo "<script>alert('Property updated successfully!'); window.location.href='admin_properties.php';</script>";
    } else {
        echo "Error updating property: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Property</title>
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

        .sidebar a.logout {
            background-color: #2c2c54;
        }

        .sidebar a.logout:hover {
            background-color: #b71c1c !important;
            transform: scale(1.05);
        }

        .sidebar a:hover, .sidebar .submenu-toggle:hover {
            background-color: #1565c0;
            transform: scale(1.05);
        }

        .submenu-container {
            margin-bottom: 10px;
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
            padding: 60px;
            overflow-y: auto;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .form-box {
            background: #2a2a40;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 150, 255, 0.2);
            width: 600px;
        }

        .form-box h2 {
            text-align: center;
            color: #76c7f3;
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            background: #1e1e3f;
            border: 1px solid #444;
            border-radius: 5px;
            color: white;
        }

        .btn-container {
            text-align: center;
            margin-top: 30px;
        }

        .btn {
            padding: 10px 20px;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 0 10px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-update { background: #28a745; }
        .btn-update:hover { background: #218838; }

        .btn-back { background: #007BFF; text-decoration: none; }
        .btn-back:hover { background: #0056b3; }

        input:focus {
            outline: none;
            border-color: #00bfff;
            box-shadow: 0 0 5px rgba(0, 191, 255, 0.5);
        }

        img.preview {
            margin-top: 10px;
            max-width: 100%;
            border-radius: 5px;
        }

        .note {
            color: #bbb;
            font-size: 12px;
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
    <div class="form-box">
        <h2>Edit Property</h2>
        <form method="POST" enctype="multipart/form-data">

            <label>Owner Name (readonly):</label>
            <input type="text" name="owner_name" value="<?php echo htmlspecialchars($property['owner_name']); ?>" readonly>

            <label>Phone (10 digits only):</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($property['phone']); ?>" required pattern="\d{10}" maxlength="10" title="Enter exactly 10 digits.">
            <p class="note">Only numbers allowed, exactly 10 digits.</p>

            <label>Email (readonly):</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($property['email']); ?>" readonly style="background-color: #222831; color: #ccc;">
            <p class="note">Must be a valid Gmail address (e.g., user123@gmail.com).</p>

            <label>Property Type:</label>
            <select name="property_type" required>
                <option value="Residential" <?php if ($property['property_type'] == 'Residential') echo 'selected'; ?>>Residential</option>
                <option value="Agriculture" <?php if ($property['property_type'] == 'Agriculture') echo 'selected'; ?>>Agriculture</option>
                <option value="Industrial" <?php if ($property['property_type'] == 'Industrial') echo 'selected'; ?>>Industrial</option>
            </select>

            <label>Location:</label>
            <input type="text" name="location" value="<?php echo htmlspecialchars($property['location']); ?>" required>

            <label>Land Area (sqm):</label>
            <input type="text" name="land_area" value="<?php echo htmlspecialchars($property['land_area']); ?>" required>

            <label>Price Range:</label>
            <input type="text" name="price_range" value="<?php echo htmlspecialchars($property['price_range']); ?>" required>

            <div class="btn-container">
                <button type="submit" class="btn btn-update">Update Property</button>
                <a href="admin_properties.php" class="btn btn-back">Back to Properties</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Submenu toggle functionality
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
