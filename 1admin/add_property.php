<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_email = isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : '';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_property'])) {
    $property_id = uniqid("PROP_");
    $owner_name = mysqli_real_escape_string($con, $_POST['owner_name']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $property_type = mysqli_real_escape_string($con, $_POST['property_type']);
    $location = mysqli_real_escape_string($con, $_POST['location']);
    $land_area = mysqli_real_escape_string($con, $_POST['land_area']);
    $price_range = mysqli_real_escape_string($con, $_POST['price_range']);
    $user_id = intval($_POST['user_id']);

    $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $allowed_types = ["jpg", "jpeg", "png", "gif", "webp"];
    $image_paths = [];

    if (!empty($_FILES['images']['name'][0])) {
        $file_count = count($_FILES['images']['name']);
        if ($file_count > 3) {
            $message = "<div style='color: red;'>You can upload a maximum of 3 images.</div>";
        } else {
            for ($i = 0; $i < $file_count; $i++) {
                $tmp_name = $_FILES['images']['tmp_name'][$i];
                $file_name = basename($_FILES['images']['name'][$i]);
                $target_file = $target_dir . $file_name;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                if (in_array($imageFileType, $allowed_types)) {
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $image_paths[] = $file_name;
                    }
                }
            }

            if (!empty($image_paths)) {
                $image_string = implode(',', $image_paths);
                $insert_query = "INSERT INTO addproperty (property_id, owner_name, phone, email, property_type, location, land_area, price_range, images, user_id)
                                 VALUES ('$property_id', '$owner_name', '$phone', '$email', '$property_type', '$location', '$land_area', '$price_range', '$image_string', $user_id)";

                if (mysqli_query($con, $insert_query)) {
                    echo "<script>alert('Property added successfully!'); window.location.href = 'admin_dashboard.php';</script>";
                    exit();
                } else {
                    $message = "<div style='color: red;'>Error adding property: " . mysqli_error($con) . "</div>";
                }
            } else {
                $message = "<div style='color: red;'>No valid images uploaded.</div>";
            }
        }
    } else {
        $message = "<div style='color: red;'>Please upload at least one image.</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Property</title>
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

        .container {
            background: #2a2a40;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 150, 255, 0.2);
            max-width: 600px;
            margin: auto;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #1e1e3f;
            color: #ffffff;
            font-size: 14px;
            outline: none;
            transition: border 0.3s, box-shadow 0.3s;
        }

        input::placeholder {
            color: #aaa;
        }

        input[readonly] {
            background-color: #2e2e4d;
            color: #ccc;
        }

        input:focus, select:focus {
            border-color: #2196f3;
            box-shadow: 0 0 5px rgba(33, 150, 243, 0.5);
        }

        button {
            background: #28a745;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 15px;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #218838;
        }

        .message {
            margin-top: 10px;
            font-weight: bold;
            text-align: center;
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
    <div class="container">
        <h3 style="text-align:center; color: #76c7f3;">Add New Property</h3>
        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Owner Name:</label>
            <input type="text" name="owner_name" value="AdminYash" required readonly>

            <label>Phone:</label>
            <input type="text" name="phone" value="9156365588" required readonly>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($admin_email); ?>" readonly>

            <label>Property Type:</label>
            <select name="property_type" required>
                <option value="Residential">Residential</option>
                <option value="Agricultural">Agricultural</option>
                <option value="Industrial">Industrial</option>
            </select>

            <label>Location:</label>
            <input type="text" name="location" required>

            <label>Land Area (sqm):</label>
            <input type="text" name="land_area" required>

            <label>Price Range:</label>
            <input type="text" name="price_range" required>

            <label>Upload Image:</label>
            <input type="file" name="images[]" accept=".jpg,.jpeg,.png,.gif,.webp" multiple required>
            <small>Max 3 images allowed</small>

            <input type="hidden" name="user_id" value="1">

            <button type="submit" name="add_property">Add Property</button>
        </form>
    </div>
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
