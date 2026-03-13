<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle notification message from remove action
$message = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : "";

// Fetch all wishlist items
$wishlist_items = [];
$wishlist_query = mysqli_query($con, "SELECT * FROM wishlist WHERE user_id = $user_id");

while ($wish = mysqli_fetch_assoc($wishlist_query)) {
    $property_id = mysqli_real_escape_string($con, $wish['property_id']);
    $type = $wish['property_type'];

    if ($type === 'sale') {
        $result = mysqli_query($con, "SELECT *, 'sale' as type FROM addproperty WHERE property_id = '$property_id'");
    } else { // rent
        $result = mysqli_query($con, "SELECT *, 'rent' as type FROM addrentproperty WHERE property_id = '$property_id'");
    }

    if ($result && mysqli_num_rows($result) > 0) {
        $property = mysqli_fetch_assoc($result);
        $wishlist_items[] = $property;
    }
}

// Check for unread admin replies
$notif_query = "SELECT COUNT(*) AS unread_count 
                FROM admin_messages 
                WHERE user_id='$user_id' AND reply != '' AND is_read = 0";
$notif_result = mysqli_query($con, $notif_query);
$unread_count = mysqli_fetch_assoc($notif_result)['unread_count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Wishlist</title>
    <style>
        /* ---------- General ---------- */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #121212;
            color: #fff;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ---------- Sidebar ---------- */
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
            line-height: 1.5;
            text-align: center;
        }

        .sidebar p {
            color: #cfd8dc;
            font-size: 14px;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar a, .submenu-toggle {
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

        .sidebar a:hover, .submenu-toggle:hover {
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
            margin-bottom: 5px;
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

        /* ---------- Main Content ---------- */
        .main-content {
            flex-grow: 1;
            background: #1e1e2f;
            padding: 40px;
            overflow-y: auto;
        }

        h2 {
            color: #00bfff;
            text-align: center;
            margin-bottom: 30px;
        }

        .wishlist-card {
            background: #2a2a40;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 200, 255, 0.2);
            margin: 0 auto 30px;
            width: 75%;
            color: #ddd;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeIn 0.6s ease;
        }

        .wishlist-card h3 {
            color: #4fc3f7;
            margin-bottom: 20px;
            text-align: center;
        }

        .wishlist-card p {
            margin: 6px 0;
            font-size: 15px;
            color: #ccc;
            text-align: center;
        }

        .remove-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background-color: #ef4444;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .remove-btn:hover {
            background-color: #c53030;
        }

        .image-slider {
            position: relative;
            width: 100%;
            max-height: 400px;
            overflow: hidden;
            border-radius: 8px;
            margin-top: 20px;
        }

        .slide-img {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: contain;
            border-radius: 6px;
            background-color: #f9f9f9;
            display: none;
        }

        .slide-img.active {
            display: block;
        }

        .image-slider button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.5);
            border: none;
            color: white;
            font-size: 20px;
            padding: 10px;
            cursor: pointer;
            z-index: 2;
            border-radius: 50%;
        }

        .prev-slide { left: 10px; }
        .next-slide { right: 10px; }

        .no-wishlist {
            text-align: center;
            color: #f87171;
            font-size: 18px;
            margin-top: 50px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ---------- Popup Notification ---------- */
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

        /* ---------- Custom Confirm ---------- */
        .confirm-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        .confirm-box {
            background: #2a2a40;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            color: #fff;
            width: 300px;
        }
        .confirm-buttons {
            margin-top: 15px;
            display: flex;
            justify-content: space-around;
        }
        .confirm-buttons button {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .confirm-yes { background: #ef4444; color: #fff; }
        .confirm-no { background: #4fc3f7; color: #000; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> 👋</h2>
    <p>Manage your properties easily</p>

    <div class="submenu-container">
        <div class="submenu-toggle">
            ➕ Sell/Rent Property
            <div class="triangle"></div>
        </div>
        <div class="submenu">
            <a href="add_property.php">Sell Property</a>
            <a href="add_rent_property.php">Rent Property</a>
        </div>
    </div>

    <div class="submenu-container">
        <div class="submenu-toggle">
            🏠 View Properties
            <div class="triangle"></div>
        </div>
        <div class="submenu">
            <a href="view_properties.php">Buy Properties</a>
            <a href="view_rent_properties.php">Rent Properties</a>
        </div>
    </div>

    <a href="search_property.php">🔍 Search Property</a>
    <a href="wishlist.php">💙 Wish List</a>
    <a href="contact_admin.php">
        📩 MSG Admin
        <?php if ($unread_count > 0): ?>
            <span class="badge"><?php echo $unread_count; ?></span>
        <?php endif; ?>
    </a>
    <a href="user_logout.php" class="logout">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2>My Wishlist</h2>

    <?php if (!empty($wishlist_items)): ?>
        <?php foreach ($wishlist_items as $row): ?>
            <?php
                $imageList = explode(",", $row['images']);
                $validImages = [];
                $folder = ($row['type'] === 'sale') ? "/jaga/landrecordsys/uploads/" : "/jaga/landrecordsys/rent_upload/";

                foreach ($imageList as $img) {
                    $trimmed = trim($img);
                    $path = $folder . $trimmed;
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
                        $validImages[] = $path;
                    }
                }

                if (empty($validImages)) {
                    $validImages[] = $folder . "default.png";
                }
            ?>
            <div class="wishlist-card">
                <h3><?php echo htmlspecialchars(ucfirst($row['type']) . " - " . $row['location']); ?></h3>
                <p><strong>Owner:</strong> <?php echo htmlspecialchars($row['owner_name']); ?></p>

                <?php if ($row['type'] === 'sale'): ?>
                    <p><strong>Price:</strong> <?php echo htmlspecialchars($row['price_range']); ?></p>
                    <p><strong>Land Area:</strong> <?php echo htmlspecialchars($row['land_area']); ?> sq. ft</p>
                <?php else: ?>
                    <p><strong>Rent Price:</strong> ₹<?php echo htmlspecialchars($row['rent_price']); ?> / month</p>
                    <p><strong>Security Deposit:</strong> ₹<?php echo htmlspecialchars($row['security_deposit']); ?></p>
                    <p><strong>Furnishing Status:</strong> <?php echo htmlspecialchars($row['furnishing_status']); ?></p>
                    <p><strong>Land Area:</strong> <?php echo htmlspecialchars($row['land_area']); ?> sq. ft</p>
                    <p><strong>Availability Date:</strong> <?php echo htmlspecialchars($row['availability_date']); ?></p>
                    <p><strong>Rental Duration:</strong> <?php echo htmlspecialchars($row['rental_duration']); ?></p>
                <?php endif; ?>

                <div class="image-slider">
                    <?php foreach ($validImages as $index => $imgPath): ?>
                        <img class="slide-img <?php echo $index === 0 ? 'active' : ''; ?>" src="<?php echo $imgPath; ?>" alt="Property Image">
                    <?php endforeach; ?>
                    <?php if(count($validImages) > 1): ?>
                        <button class="prev-slide">⬅️</button>
                        <button class="next-slide">➡️</button>
                    <?php endif; ?>
                </div>

                <a class="remove-btn" href="remove_from_wishlist.php?property_id=<?php echo $row['property_id']; ?>&type=<?php echo $row['type']; ?>" onclick="return showConfirm(event, this.href)">❌ Remove</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-wishlist">Your wishlist is currently empty 💔</p>
    <?php endif; ?>
</div>

<!-- Popup -->
<div class="popup" id="wishlistPopup"></div>

<!-- Confirm Box -->
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <p>Are you sure you want to remove this property from wishlist?</p>
        <div class="confirm-buttons">
            <button class="confirm-yes" id="confirmYes">Yes</button>
            <button class="confirm-no" id="confirmNo">No</button>
        </div>
    </div>
</div>

<script>
    // Sidebar submenu toggle
    document.querySelectorAll('.submenu-toggle').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const submenu = toggle.nextElementSibling;
            const triangle = toggle.querySelector('.triangle');
            submenu.style.display = submenu.style.display === 'flex' ? 'none' : 'flex';
            triangle.classList.toggle('open');
        });
    });

    // Image slider
    document.querySelectorAll('.image-slider').forEach(slider => {
        const slides = slider.querySelectorAll('.slide-img');
        let index = 0;

        const showSlide = i => slides.forEach((img, idx) => img.classList.toggle('active', idx === i));

        const prevBtn = slider.querySelector('.prev-slide');
        const nextBtn = slider.querySelector('.next-slide');

        if(prevBtn) prevBtn.addEventListener('click', e => { e.preventDefault(); index = (index - 1 + slides.length) % slides.length; showSlide(index); });
        if(nextBtn) nextBtn.addEventListener('click', e => { e.preventDefault(); index = (index + 1) % slides.length; showSlide(index); });
    });

    // Popup Notification
    document.addEventListener("DOMContentLoaded", function () {
        const popup = document.getElementById("wishlistPopup");
        const message = "<?php echo $message; ?>";
        if (message) {
            popup.textContent = message;
            popup.style.display = "block";
            setTimeout(() => {
                popup.style.display = "none";
            }, 3000);
        }
    });

    // Custom Confirm
    let confirmHref = "";
    function showConfirm(e, href) {
        e.preventDefault();
        confirmHref = href;
        document.getElementById("confirmOverlay").style.display = "flex";
        return false;
    }
    document.getElementById("confirmYes").addEventListener("click", () => {
        window.location.href = confirmHref;
    });
    document.getElementById("confirmNo").addEventListener("click", () => {
        document.getElementById("confirmOverlay").style.display = "none";
    });
</script>

</body>
</html>
