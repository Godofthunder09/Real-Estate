<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"] ?? "User ";

// Check for unread admin replies
$notif_query = "SELECT COUNT(*) AS unread_count 
                FROM admin_messages 
                WHERE user_id='$user_id' AND reply != '' AND is_read = 0";
$notif_result = mysqli_query($con, $notif_query);
$unread_count = mysqli_fetch_assoc($notif_result)['unread_count'] ?? 0;

// Filters
$filter_location = $_GET['location'] ?? '';
$filter_type = $_GET['type'] ?? '';
$filter_min_price = $_GET['min_price'] ?? '';
$filter_max_price = $_GET['max_price'] ?? '';
$filter_furnishing = $_GET['furnishing'] ?? '';
$filter_duration = $_GET['duration'] ?? '';

// Build query
$query = "SELECT * FROM addrentproperty WHERE 1";

if (!empty($filter_location)) {
    $query .= " AND location LIKE '%" . mysqli_real_escape_string($con, $filter_location) . "%'";
}
if (!empty($filter_type)) {
    $query .= " AND property_type = '" . mysqli_real_escape_string($con, $filter_type) . "'";
}
if (!empty($filter_min_price)) {
    $query .= " AND rent_price >= '" . mysqli_real_escape_string($con, $filter_min_price) . "'";
}
if (!empty($filter_max_price)) {
    $query .= " AND rent_price <= '" . mysqli_real_escape_string($con, $filter_max_price) . "'";
}
if (!empty($filter_furnishing)) {
    $query .= " AND furnishing_status = '" . mysqli_real_escape_string($con, $filter_furnishing) . "'";
}
if (!empty($filter_duration)) {
    $query .= " AND rental_duration LIKE '%" . mysqli_real_escape_string($con, $filter_duration) . "%'";
}

$query .= " ORDER BY id DESC";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Rental Properties</title>
<style>
/* ----------------- Sidebar & Admin Theme ----------------- */
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

/* ----------------- Main Content & Filters ----------------- */
.main-content {
    flex-grow: 1;
    background: #1e1e2f;
    padding: 40px;
    overflow-y: auto;
}

h2 {
    text-align: center;
    color: #76c7f3;
}

.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
    margin-bottom: 30px;
}

.filter-form input, .filter-form select {
    padding: 10px;
    border: none;
    border-radius: 5px;
    background: #333;
    color: white;
}

.filter-form button {
    padding: 10px 20px;
    background: #0288d1;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.filter-form button:hover {
    background: #026aa7;
}

/* ----------------- Property Cards ----------------- */
.property-list {
    display: flex;
    flex-direction: column;
    gap: 30px;
    align-items: center;
}

.property-card {
    background: #2a2a40;
    width: 600px;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 0 15px rgba(0, 150, 255, 0.1);
    color: #ddd;
    position: relative;
}

.property-card h3 { color: #4fc3f7; margin-bottom: 10px; }
.property-card p { margin: 4px 0; color: #ccc; }

.image-slider {
    position: relative;
    width: 100%;
    max-height: 300px;
    overflow: hidden;
}

.slide-img {
    width: 100%;
    height: auto;
    max-height: 300px;
    object-fit: contain;
    border-radius: 5px;
    background-color: rgb(111, 174, 203);
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

.wishlist-btn {
    display: inline-block;
    margin-top: 12px;
    padding: 8px 16px;
    background-color: #ff4081;
    color: white;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
}

.wishlist-btn:hover { background-color: #e91e63; }

.popup {
    display: none;
    position: fixed;
    top: 20px;
    right: 20px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 15px;
    border-radius: 5px;
    z-index: 1000;
}
</style>
</head>
<body>

<div class="sidebar">
    <h2>Welcome, <?php echo htmlspecialchars($user_name); ?> 👋</h2>
    <p>Manage your properties easily.</p>

    <div class="submenu-container">
        <div class="submenu-toggle">
            ➕ Sell/Rent Property
            <div class="triangle"></div>
        </div>
        <div class="submenu">
            <a href="add_property.php">🏠 Sell Property</a>
            <a href="add_rent_property.php">🏘 Rent Property</a>
        </div>
    </div>

    <div class="submenu-container">
        <div class="submenu-toggle">
            📋 View Properties
            <div class="triangle open"></div>
        </div>
        <div class="submenu" style="display:flex;">
            <a href="view_properties.php">🏠 Buy Property</a>
            <a href="view_rent_properties.php" style="background-color:#0288d1;">🏘 Rent Property</a>
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

<div class="main-content">
    <h2>View All Rental Properties</h2>

    <form class="filter-form" method="GET">
        <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($filter_location); ?>">
        <select name="type">
            <option value="">All Types</option>
            <option value="Residential" <?php if ($filter_type === 'Residential') echo 'selected'; ?>>Residential</option>
            <option value="Commercial" <?php if ($filter_type === 'Commercial') echo 'selected'; ?>>Commercial</option>
            <option value="Industrial" <?php if ($filter_type === 'Industrial') echo 'selected'; ?>>Industrial</option>
        </select>
        <input type="number" name="min_price" placeholder="Min Rent" value="<?php echo htmlspecialchars($filter_min_price); ?>">
        <input type="number" name="max_price" placeholder="Max Rent" value="<?php echo htmlspecialchars($filter_max_price); ?>">
        <select name="furnishing">
            <option value="">Any Furnishing</option>
            <option value="Furnished" <?php if ($filter_furnishing === 'Furnished') echo 'selected'; ?>>Furnished</option>
            <option value="Semi-Furnished" <?php if ($filter_furnishing === 'Semi-Furnished') echo 'selected'; ?>>Semi-Furnished</option>
            <option value="Unfurnished" <?php if ($filter_furnishing === 'Unfurnished') echo 'selected'; ?>>Unfurnished</option>
        </select>
        <input type="text" name="duration" placeholder="Rental Duration" value="<?php echo htmlspecialchars($filter_duration); ?>">
        <button type="submit">🔍 Filter</button>
    </form>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="property-list">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="property-card">
                    <h3>Property ID: <?php echo htmlspecialchars($row['property_id']); ?></h3>
                    <p><strong>Owner:</strong> <?php echo htmlspecialchars($row['owner_name']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($row['property_type']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                    <p><strong>Land Area:</strong> <?php echo htmlspecialchars($row['land_area']); ?> sq. ft</p>
                    <p><strong>Rent Price:</strong> ₹<?php echo htmlspecialchars($row['rent_price']); ?></p>
                    <p><strong>Security Deposit:</strong> ₹<?php echo htmlspecialchars($row['security_deposit']); ?></p>
                    <p><strong>Furnishing:</strong> <?php echo htmlspecialchars($row['furnishing_status']); ?></p>
                    <p><strong>Available From:</strong> <?php echo htmlspecialchars($row['availability_date']); ?></p>
                    <p><strong>Rental Duration:</strong> <?php echo htmlspecialchars($row['rental_duration']); ?></p>

                    <?php 
                    $images = explode(",", $row['images']);
                    $imagePaths = array_map(function($img) {
                        $img = trim($img);
                        return str_starts_with($img, "rent_upload/") ? $img : "rent_upload/" . $img;
                    }, $images);
                    ?>

                    <?php if (!empty($row['images'])): ?>
                        <div class="image-slider">
                            <?php foreach ($imagePaths as $index => $imgPath): ?>
                                <img class="slide-img" src="/jaga/landrecordsys/<?php echo htmlspecialchars($imgPath); ?>" 
                                     alt="Property Image <?php echo $index + 1; ?>" 
                                     onerror="this.onerror=null; this.src='/jaga/landrecordsys/uploads/default.jpg';" 
                                     style="<?php echo $index === 0 ? '' : 'display:none;'; ?>">
                            <?php endforeach; ?>
                            <?php if (count($imagePaths) > 1): ?>
                                <button class="prev-slide">⬅️</button>
                                <button class="next-slide">➡️</button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <p><em>No Image Available</em></p>
                    <?php endif; ?>

                    <a class="wishlist-btn" href="javascript:void(0);" data-property-id="<?php echo urlencode($row['property_id']); ?>" onclick="addToWishlist(this)">💙 Add to Wishlist</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p style="text-align:center;color:red;">No rental properties found.</p>
    <?php endif; ?>
</div>

<div class="popup" id="wishlistPopup">Property added to wishlist successfully!</div>

<script>
document.querySelectorAll('.submenu-toggle').forEach(toggle => {
    toggle.addEventListener('click', () => {
        const submenu = toggle.nextElementSibling;
        const triangle = toggle.querySelector('.triangle');
        submenu.style.display = submenu.style.display === 'flex' ? 'none' : 'flex';
        triangle.classList.toggle('open');
    });
});

document.querySelectorAll('.image-slider').forEach(slider => {
    const slides = slider.querySelectorAll('.slide-img');
    let index = 0;
    const showSlide = i => slides.forEach((img, idx) => img.style.display = idx === i ? 'block' : 'none');
    const prevBtn = slider.querySelector('.prev-slide');
    const nextBtn = slider.querySelector('.next-slide');
    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => { index = (index-1+slides.length)%slides.length; showSlide(index); });
        nextBtn.addEventListener('click', () => { index = (index+1)%slides.length; showSlide(index); });
    }
});

function addToWishlist(element) {
    const propertyId = element.getAttribute("data-property-id");
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "add_to_wishlist.php?property_id=" + propertyId + "&type=rent", true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            showPopup(response.message);
        } else {
            alert('Failed to add property to wishlist.');
        }
    };
    xhr.send();
}

function showPopup(message) {
    const popup = document.getElementById("wishlistPopup");
    popup.textContent = message;
    popup.style.display = "block";
    setTimeout(() => { popup.style.display = "none"; }, 3000);
}
</script>
</body>
</html>
