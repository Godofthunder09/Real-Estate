<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

// Check if property ID is provided
if (!isset($_GET['id'])) {
    echo "Property ID is missing.";
    exit();
}

// Secure the property ID against SQL Injection
$property_id = mysqli_real_escape_string($con, $_GET['id']);

// Fetch property details
$query = "SELECT * FROM addproperty WHERE property_id = '$property_id'";
$result = mysqli_query($con, $query);
$property = mysqli_fetch_assoc($result);

if (!$property) {
    echo "Property not found.";
    exit();
}

// Set default image path
$default_image = "../uploads/default.png";

// Check if the property has an image and if it exists in the uploads folder
$imagePath = (!empty($property['images']) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/uploads/" . $property['images'])) 
    ? "../uploads/" . htmlspecialchars($property['images']) 
    : $default_image;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Property</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #111;
        margin: 0;
        padding: 20px;
        color: #fff;
    }

    .container {
        background: #1c1c1c;
        padding: 25px;
        box-shadow: 0 0 20px rgba(0, 229, 255, 0.15);
        max-width: 700px;
        margin: auto;
        border-radius: 12px;
        text-align: center;
        border: 1px solid #00e5ff33;
    }

    h2 {
        color: #00e5ff;
        font-size: 28px;
        margin-bottom: 25px;
    }

    p {
        font-size: 16px;
        margin: 8px 0;
        text-align: left;
        line-height: 1.6;
    }

    strong {
        color: #00e5ff;
    }

    .property-img {
    width: 100%;
    height: auto;
    max-height: 500px; /* Increase if it still looks cut off */
    object-fit: contain; /* Try "cover" or "contain" */
    margin-top: 20px;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0, 229, 255, 0.2);
    transition: transform 0.3s ease-in-out;
}

    .property-img:hover {
        transform: scale(1.03);
    }

    .btn-container {
        text-align: center;
        margin-top: 25px;
    }

    .btn {
        padding: 12px 20px;
        text-decoration: none;
        color: #000;
        background-color: #00e5ff;
        border-radius: 6px;
        margin: 5px;
        display: inline-block;
        font-size: 15px;
        font-weight: bold;
        transition: 0.3s ease-in-out;
        border: 2px solid transparent;
        box-shadow: 0 0 10px rgba(0, 229, 255, 0.5);
    }

    .btn:hover {
        background-color: transparent;
        color: #00e5ff;
        border-color: #00e5ff;
        box-shadow: 0 0 15px rgba(0, 229, 255, 0.7);
    }

    /* Optional: Color-coding for different buttons if you still want them distinct */
    .btn-edit {
        background-color: #28a745;
        border-color: #28a745;
        color: #fff;
    }

    .btn-edit:hover {
        background-color: transparent;
        color: #28a745;
        box-shadow: 0 0 10px rgba(40, 167, 69, 0.6);
    }

    .btn-delete {
        background-color: #dc3545;
        border-color: #dc3545;
        color: #fff;
    }

    .btn-delete:hover {
        background-color: transparent;
        color: #dc3545;
        box-shadow: 0 0 10px rgba(220, 53, 69, 0.6);
    }
</style>

</head>
<body>

<div class="container">
    <h2>Property Details</h2>

    <p><strong>Property ID:</strong> <?php echo htmlspecialchars($property['property_id']); ?></p>
    <p><strong>Owner Name:</strong> <?php echo htmlspecialchars($property['owner_name']); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($property['phone']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($property['email']); ?></p>
    <p><strong>Property Type:</strong> <?php echo htmlspecialchars($property['property_type']); ?></p>
    <p><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
    <p><strong>Land Area:</strong> <?php echo htmlspecialchars($property['land_area']); ?> sqm</p>
    <p><strong>Price Range:</strong> <?php echo htmlspecialchars($property['price_range']); ?></p>

    <!-- Property Image -->
    <img class="property-img" src="<?php echo $imagePath; ?>" alt="Property Image"
         onerror="this.onerror=null; this.src='<?php echo $default_image; ?>';">
    <div class="btn-container">
        <a href="admin_dashboard.php" class="btn btn-back">Back to Dashboard</a>
        <a href="edit_property.php?id=<?php echo urlencode($property['property_id']); ?>" class="btn btn-edit">Edit</a>
        <a href="delete_property.php?id=<?php echo urlencode($property['property_id']); ?>" 
           class="btn btn-delete" 
           onclick="return confirm('Are you sure you want to delete this property?');">
           Delete
        </a>
    </div>
</div>

</body>
</html>
