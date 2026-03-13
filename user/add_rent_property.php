<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch logged-in user's details
$user_id = $_SESSION['user_id'];
$query_user = "SELECT name, email FROM users WHERE id = '$user_id'";
$result_user = mysqli_query($con, $query_user);
$user = mysqli_fetch_assoc($result_user);
$owner_name = $user['name'];
$email = $user['email'];

// Check for unread admin replies
$notif_query = "SELECT COUNT(*) AS unread_count 
                FROM admin_messages 
                WHERE user_id='$user_id' AND reply != '' AND is_read = 0";
$notif_result = mysqli_query($con, $notif_query);
$unread_count = mysqli_fetch_assoc($notif_result)['unread_count'] ?? 0;

$message = "";

if (isset($_POST['submit'])) {
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $property_type = mysqli_real_escape_string($con, $_POST['property_type']);
    $location = mysqli_real_escape_string($con, $_POST['location']);
    $land_area = mysqli_real_escape_string($con, $_POST['land_area']);
    $rent_price = mysqli_real_escape_string($con, $_POST['rent_price']);
    $security_deposit = mysqli_real_escape_string($con, $_POST['security_deposit']);
    $furnishing_status = mysqli_real_escape_string($con, $_POST['furnishing_status']);
    $availability_date = mysqli_real_escape_string($con, $_POST['availability_date']);
    $rental_duration = mysqli_real_escape_string($con, $_POST['rental_duration']);

    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $message = "Phone number must be exactly 10 digits.";
    } else {
        $property_id = uniqid("RENT_");

        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/rent_upload/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $allowed_images = $_FILES['images'];
        $total_images = count($allowed_images['name']);

        if ($total_images > 3) {
            $message = "You can upload a maximum of 3 images.";
        } else {
            $uploaded_files = [];

            for ($i = 0; $i < $total_images; $i++) {
                if (!empty($allowed_images['name'][$i])) {
                    $image_name = time() . "_" . basename($allowed_images['name'][$i]);
                    $target_file = $upload_dir . $image_name;

                    if (move_uploaded_file($allowed_images["tmp_name"][$i], $target_file)) {
                        $uploaded_files[] = $image_name;
                    }
                }
            }

            $image_paths = implode(",", $uploaded_files);

            $query = "INSERT INTO addrentproperty 
            (property_id, owner_name, phone, email, property_type, location, land_area, images, rent_price, security_deposit, furnishing_status, availability_date, rental_duration, user_id)
            VALUES 
            ('$property_id', '$owner_name', '$phone', '$email', '$property_type', '$location', '$land_area', '$image_paths', '$rent_price', '$security_deposit', '$furnishing_status', '$availability_date', '$rental_duration', '$user_id')";

            if (mysqli_query($con, $query)) {
                $message = "Property added successfully for rent! Property ID: $property_id";
            } else {
                $message = "Error: " . mysqli_error($con);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Rent Property</title>
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
        position: relative;
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

    .triangle.open { transform: rotate(180deg); }

    .main-content {
        flex-grow: 1;
        background: #1e1e2f;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 60px;
        overflow-y: auto;
    }

    .container {
        width: 600px;
        background: #2a2a40;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0, 150, 255, 0.2);
    }

    h2 { color: #76c7f3; text-align: center; }
    form { display: flex; flex-direction: column; }
    label { font-weight: bold; margin-top: 10px; color: #ccc; }
    input, select, textarea {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: none;
        border-radius: 5px;
        background: #444;
        color: white;
        font-size: 16px;
    }
    input[type="file"] { background: #222; }
    input[readonly] { background-color: #333; }
    button {
        margin-top: 20px;
        padding: 12px;
        background: #0288d1;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
    }
    button:hover { background: #026aa7; }
    .message { margin-top: 10px; text-align: center; }
    .badge {
        position: absolute;
        top: 5px;
        right: 25px;
        background: red;
        color: white;
        font-size: 12px;
        padding: 2px 6px;
        border-radius: 10px;
    }

    /* Popup notification */
    .popup {
        display: none;
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(0, 0, 0, 0.85);
        color: #fff;
        padding: 15px 20px;
        border-radius: 8px;
        font-size: 15px;
        z-index: 1000;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
</style>
</head>
<body>

<div class="sidebar">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?> 👋</h2>
    <p>Manage your properties easily.</p>

    <div class="submenu-container">
        <div class="submenu-toggle">➕ Sell/Rent Property<div class="triangle"></div></div>
        <div class="submenu">
            <a href="add_property.php">🏠 Sell Property</a>
            <a href="add_rent_property.php">🏘 Rent Property</a>
        </div>
    </div>

    <div class="submenu-container">
        <div class="submenu-toggle">📋 View Properties<div class="triangle"></div></div>
        <div class="submenu">
            <a href="view_properties.php">🏠 Buy Property</a>
            <a href="view_rent_properties.php">🏘 Rent Property</a>
        </div>
    </div>

    <a href="search_property.php">🔍 Search Property</a>
    <a href="wishlist.php">💙 Wish List</a>
    <a href="contact_admin.php">📩 MSG Admin
        <?php if ($unread_count > 0): ?>
            <span class="badge"><?php echo $unread_count; ?></span>
        <?php endif; ?>
    </a>
    <a href="user_logout.php" class="logout">🚪 Logout</a>
</div>

<div class="main-content">
    <div class="container">
        <h2>Add Rent Property</h2>

        <form method="POST" enctype="multipart/form-data">
            <label>Owner Name:</label>
            <input type="text" name="owner_name" value="<?php echo $owner_name; ?>" readonly>
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo $email; ?>" readonly>
            <label>Phone:</label>
            <input type="text" name="phone" pattern="\d{10}" maxlength="10" title="Enter exactly 10 digits" required>
            <label>Property Type:</label>
            <select name="property_type" required>
                <option value="">--Select--</option>
                <option value="Apartment">Apartment</option>
                <option value="House">House</option>
                <option value="Commercial">Commercial</option>
            </select>
            <label>Location:</label>
            <textarea name="location" required></textarea>
            <label>Land Area (sq ft):</label>
            <input type="number" name="land_area" step="0.01" required>
            <label>Rent Price:</label>
            <input type="number" name="rent_price" step="0.01" required>
            <label>Security Deposit:</label>
            <input type="number" name="security_deposit" step="0.01">
            <label>Furnishing Status:</label>
            <select name="furnishing_status">
                <option value="Furnished">Furnished</option>
                <option value="Semi-Furnished">Semi-Furnished</option>
                <option value="Unfurnished" selected>Unfurnished</option>
            </select>
            <label>Availability Date:</label>
            <input type="date" name="availability_date" required>
            <label>Rental Duration:</label>
            <input type="text" name="rental_duration" required>
            <label>Upload Images (Max 3):</label>
            <input type="file" name="images[]" multiple accept="image/*" required>
            <button type="submit" name="submit">Add Property</button>
        </form>
    </div>
</div>

<!-- Popup -->
<div class="popup" id="propertyPopup"></div>

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

    // Popup notification logic
    document.addEventListener("DOMContentLoaded", function () {
        const popup = document.getElementById("propertyPopup");
        const message = "<?php echo $message; ?>";
        if (message) {
            popup.textContent = message;
            popup.style.display = "block";
            setTimeout(() => {
                popup.style.display = "none";
            }, 3000);
        }
    });
</script>

</body>
</html>
