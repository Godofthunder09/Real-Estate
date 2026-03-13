<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: user_login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

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
    <title>User Dashboard</title>
    <!-- Link to external CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?> 👋</h2>
        <p>Manage your properties easily.</p>

        <!-- Sell/Rent with triangle -->
        <a onclick="toggleSubmenu('addSellMenu', this)">
            ➕ Sell/Rent Property 
            <span class="triangle"></span>
        </a>
        <div id="addSellMenu" class="submenu">
            <a href="add_property.php" class="submenu-link" data-parent="addSellMenu">🏠 Sell Property</a>
            <a href="add_rent_property.php" class="submenu-link" data-parent="addSellMenu">🏘 Rent Property</a>
        </div>

        <!-- View Properties with triangle -->
        <a onclick="toggleSubmenu('viewMenu', this)">
            📋 View Properties 
            <span class="triangle"></span>
        </a>
        <div id="viewMenu" class="submenu">
            <a href="view_properties.php" class="submenu-link" data-parent="viewMenu">🏠 Buy Property</a>
            <a href="view_rent_properties.php" class="submenu-link" data-parent="viewMenu">🏘 Rent Property</a>
        </div>

        <a href="search_property.php">🔍 Search Property</a>
        <a href="wishlist.php" class="wishlist">💙 Wish List</a>
        <a href="contact_admin.php">
            📩 MSG Admin
            <?php if ($unread_count > 0): ?>
                <span class="badge"><?php echo $unread_count; ?></span>
            <?php endif; ?>
        </a>
        <a href="user_logout.php" class="logout">🚪 Logout</a>
    </div>

    <div class="main-content">
        <div class="welcome-box">
            <h1>Smart Land Management System</h1>
            <p>Welcome to your dashboard. Use the side menu to manage properties, search, or log out.</p>
        </div>
    </div>

    <script>
        function toggleSubmenu(id, element) {
            var submenu = document.getElementById(id);
            var triangle = element.querySelector(".triangle");

            if (submenu.style.display === "block") {
                submenu.style.display = "none";
                triangle.classList.remove("down");
            } else {
                submenu.style.display = "block";
                triangle.classList.add("down");
            }
        }

        // Close submenu when a submenu link is clicked
        document.querySelectorAll('.submenu-link').forEach(link => {
            link.addEventListener('click', function(e) {
                let parentId = this.getAttribute('data-parent');
                let parentMenu = document.getElementById(parentId);
                let parentToggle = parentMenu.previousElementSibling.querySelector('.triangle');
                parentMenu.style.display = 'none';
                parentToggle.classList.remove('down');
            });
        });
    </script>

</body>
</html>
