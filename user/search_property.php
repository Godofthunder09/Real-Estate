<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

$saleResult = null;
$rentResult = null;
$errorMessage = "";

// Ensure user login check
if (!isset($_SESSION["user_id"])) {
    header("Location: user_login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Unread admin replies count
$notif_query = "SELECT COUNT(*) AS unread_count 
                FROM admin_messages 
                WHERE user_id='$user_id' AND reply != '' AND is_read = 0";
$notif_result = mysqli_query($con, $notif_query);
$unread_count = mysqli_fetch_assoc($notif_result)['unread_count'] ?? 0;

function resolveImagePaths($imagesString) {
    $resolved = [];
    if ($imagesString === null) return $resolved;
    $tokens = [];
    $maybeJson = trim($imagesString);
    if ($maybeJson !== '' && $maybeJson[0] === '[') {
        $arr = json_decode($maybeJson, true);
        if (is_array($arr)) foreach ($arr as $v) $tokens[] = (string)$v;
    }
    if (empty($tokens)) $tokens = explode(',', (string)$imagesString);

    $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
    $webRoots = [
        "/jaga/landrecordsys/uploads/",
        "/jaga/landrecordsys/uploads/rent/",
        "/jaga/landrecordsys/uploads/properties/",
        "/jaga/landrecordsys/rent_upload/",
        "/uploads/",
        "/uploads/rent/",
    ];

    foreach ($tokens as $raw) {
        $img = trim((string)$raw);
        if ($img === '') continue;
        if (preg_match('#^https?://#i', $img)) { $resolved[] = $img; continue; }
        if (strpos($img, '/') === 0) { $resolved[] = $img; continue; }
        if (preg_match('#^[A-Z]:\\\\#i', $img) || strpos($img, '\\') !== false) {
            $normalized = str_replace('\\', '/', $img);
            if (stripos($normalized, 'rent_upload/') !== false) {
                $filename = basename($normalized);
                $resolved[] = "/jaga/landrecordsys/rent_upload/" . $filename;
                continue;
            }
        }
        $chosen = null;
        foreach ($webRoots as $webBase) {
            $webPath = rtrim($webBase, '/') . '/' . $img;
            $fsPath  = $docRoot . $webPath;
            if (@file_exists($fsPath)) { $chosen = $webPath; break; }
        }
        if ($chosen === null) $chosen = rtrim($webRoots[0], '/') . '/' . $img;
        $resolved[] = $chosen;
    }
    return array_values(array_unique($resolved));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_id = trim($_POST['property_id'] ?? '');
    if ($property_id === '') {
        $errorMessage = "Please enter a valid Property ID.";
    } else {
        $query_sale = "SELECT * FROM addproperty WHERE property_id = ?";
        if ($stmt_sale = mysqli_prepare($con, $query_sale)) {
            mysqli_stmt_bind_param($stmt_sale, "s", $property_id);
            mysqli_stmt_execute($stmt_sale);
            $result_sale = mysqli_stmt_get_result($stmt_sale);
            if ($result_sale && mysqli_num_rows($result_sale) > 0) $saleResult = mysqli_fetch_assoc($result_sale);
            mysqli_stmt_close($stmt_sale);
        }

        $query_rent = "SELECT * FROM addrentproperty WHERE property_id = ?";
        if ($stmt_rent = mysqli_prepare($con, $query_rent)) {
            mysqli_stmt_bind_param($stmt_rent, "s", $property_id);
            mysqli_stmt_execute($stmt_rent);
            $result_rent = mysqli_stmt_get_result($stmt_rent);
            if ($result_rent && mysqli_num_rows($result_rent) > 0) $rentResult = mysqli_fetch_assoc($result_rent);
            mysqli_stmt_close($stmt_rent);
        }

        if (!$saleResult && !$rentResult) $errorMessage = "No property found with this Property ID.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Search Property</title>
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

    .sidebar a.logout { background-color: #2c2c54; }
    .sidebar a.logout:hover { background-color: #b71c1c !important; transform: scale(1.05); }

    .submenu { display: none; flex-direction: column; margin-left: 15px; }
    .submenu a { background-color: #1a1a2f; font-size: 14px; }
    .submenu a:hover { background-color: #1565c0; }

    .triangle {
        width: 0; height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 8px solid white;
        transition: transform 0.3s ease;
    }
    .triangle.open { transform: rotate(180deg); }

    .main-content {
        flex-grow: 1;
        background: #1e1e2f;
        padding: 40px;
        overflow-y: auto;
    }

    .search-box { text-align:center; margin-bottom:30px; }
    .search-box input[type="text"] { width:50%; padding:10px; border-radius:5px; border:none; background:#333; color:white; }
    .search-box input[type="submit"] { padding:10px 20px; background:#0288d1; color:white; border:none; cursor:pointer; border-radius:5px; margin-left:10px; }
    .search-box input[type="submit"]:hover { background:#026aa7; }

    .error-msg { color:red; font-size:16px; margin-top:10px; text-align:center; }

    .property-card { background:#2a2a40; padding:25px 30px; border-radius:12px; box-shadow:0 0 20px rgba(0,200,255,0.2); margin:auto; width:70%; color:#ddd; animation:fadeIn 0.6s ease; margin-bottom:30px; }
    .property-card h3 { color:#4fc3f7; margin-bottom:20px; text-align:center; }
    .property-card p { margin:6px 0; font-size:15px; color:#ccc; }
    .image-slider { position:relative; width:100%; max-height:300px; overflow:hidden; margin-top:15px; }
    .slide-img { width:100%; height:auto; max-height:300px; object-fit:contain; border-radius:6px; background-color:#f9f9f9; }
    .image-slider button { position:absolute; top:50%; transform:translateY(-50%); background:rgba(0,0,0,0.5); border:none; color:white; font-size:20px; padding:10px; cursor:pointer; z-index:2; border-radius:50%; }
    .prev-slide { left:10px; }
    .next-slide { right:10px; }
    @keyframes fadeIn { from{opacity:0; transform:translateY(20px);} to{opacity:1; transform:translateY(0);} }
</style>
</head>
<body>

<div class="sidebar">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"] ?? "User"); ?> 👋</h2>
    <p>Search for properties by ID.</p>

    
    <div class="submenu-toggle">
        ➕ Sell/Rent Property
        <div class="triangle"></div>
    </div>
    <div class="submenu">
        <a href="add_property.php">Sell Property</a>
        <a href="add_rent_property.php">Rent Property</a>
    </div>

    <div class="submenu-toggle">
        🏠 View Properties
        <div class="triangle"></div>
    </div>
    <div class="submenu">
        <a href="view_properties.php">Buy Property</a>
        <a href="view_rent_properties.php">Rent Property</a>
    </div>

    <a href="search_property.php">🔍 Search Property</a>
    <a href="wishlist.php">💙 Wish List <?php if ($unread_count > 0): ?><span class="badge"><?php echo $unread_count; ?></span><?php endif; ?></a>
    <a href="contact_admin.php">📩 MSG Admin</a>
    <a href="user_logout.php" class="logout">🚪 Logout</a>
</div>

<div class="main-content">
    <h2>Search Property</h2>
    <form method="POST" class="search-box">
        <input type="text" name="property_id" placeholder="Enter Property ID" required>
        <input type="submit" value="Search">
    </form>

    <?php if (!empty($errorMessage)): ?>
        <p class="error-msg"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <?php
    function displayProperty($data, $type) {
        $imagePaths = resolveImagePaths($data['images'] ?? '');
        ?>
        <div class="property-card">
            <h3><?php echo $type === 'sale' ? 'Sale Property Details' : 'Rental Property Details'; ?></h3>
            <p><strong>Property ID:</strong> <?php echo htmlspecialchars($data['property_id'] ?? ''); ?></p>
            <p><strong>Owner Name:</strong> <?php echo htmlspecialchars($data['owner_name'] ?? ''); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($data['phone'] ?? ''); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($data['email'] ?? ''); ?></p>
            <p><strong>Property Type:</strong> <?php echo htmlspecialchars($data['property_type'] ?? ''); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($data['location'] ?? ''); ?></p>
            <p><strong>Land Area:</strong> <?php echo htmlspecialchars($data['land_area'] ?? ''); ?> sq. ft</p>

            <?php if ($type === 'sale'): ?>
                <p><strong>Price Range:</strong> <?php echo htmlspecialchars($data['price_range'] ?? ''); ?></p>
            <?php else: ?>
                <p><strong>Rent Price:</strong> ₹<?php echo htmlspecialchars($data['rent_price'] ?? ''); ?></p>
                <p><strong>Security Deposit:</strong> ₹<?php echo htmlspecialchars($data['security_deposit'] ?? ''); ?></p>
                <p><strong>Furnishing Status:</strong> <?php echo htmlspecialchars($data['furnishing_status'] ?? ''); ?></p>
                <p><strong>Availability Date:</strong> <?php echo htmlspecialchars($data['availability_date'] ?? ''); ?></p>
                <p><strong>Rental Duration:</strong> <?php echo htmlspecialchars($data['rental_duration'] ?? ''); ?></p>
            <?php endif; ?>

            <?php if (!empty($imagePaths)): ?>
                <div class="image-slider">
                    <?php foreach ($imagePaths as $index => $imgPath): ?>
                        <img class="slide-img" src="<?php echo htmlspecialchars($imgPath); ?>" alt="Image <?php echo $index + 1; ?>" onerror="this.onerror=null; this.src='/jaga/landrecordsys/uploads/default.png';" style="<?php echo $index===0?'':'display:none;'; ?>">
                    <?php endforeach; ?>
                    <?php if (count($imagePaths) > 1): ?>
                        <button class="prev-slide">⬅️</button>
                        <button class="next-slide">➡️</button>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="image-slider">
                    <img class="slide-img" src="/jaga/landrecordsys/uploads/default.png" alt="No Image Available">
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    if ($saleResult) displayProperty($saleResult, 'sale');
    if ($rentResult) displayProperty($rentResult, 'rent');
    ?>
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

    document.querySelectorAll('.image-slider').forEach(slider => {
        const slides = slider.querySelectorAll('.slide-img');
        let index = 0;
        const showSlide = i => slides.forEach((img, idx) => img.style.display = idx === i ? 'block' : 'none');
        const prevBtn = slider.querySelector('.prev-slide');
        const nextBtn = slider.querySelector('.next-slide');
        if (prevBtn && nextBtn) {
            prevBtn.addEventListener('click', e => { e.preventDefault(); index=(index-1+slides.length)%slides.length; showSlide(index); });
            nextBtn.addEventListener('click', e => { e.preventDefault(); index=(index+1)%slides.length; showSlide(index); });
        }
    });
</script>

</body>
</html>
