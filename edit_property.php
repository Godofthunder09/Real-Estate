<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_GET['id'])) {
    echo "Property ID is missing.";
    exit();
}

$property_id = $_GET['id'];

// Fetch existing property data
$query = "SELECT * FROM addproperty WHERE property_id = '$property_id'";
$result = mysqli_query($con, $query);
$property = mysqli_fetch_assoc($result);

if (!$property) {
    echo "Property not found.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner_name = $_POST['owner_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $property_type = $_POST['property_type'];
    $location = $_POST['location'];
    $land_area = $_POST['land_area'];
    $price_range = $_POST['price_range'];

    // Default to existing image path
    $image_path = $property['images'];

    // Handle Image Upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/uploads/"; // Absolute path
        $file_name = time() . "_" . basename($_FILES["image"]["name"]); // Unique filename
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Allow only certain file formats
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = "uploads/" . $file_name;  // Save relative path to DB
            } else {
                echo "<script>alert('Error uploading file. Check folder permissions.');</script>";
            }
        } else {
            echo "<script>alert('Only JPG, JPEG, PNG, & GIF files are allowed.');</script>";
        }
    }

    // Update property details in the database
    $update_query = "UPDATE addproperty 
                     SET owner_name='$owner_name', phone='$phone', email='$email', 
                         property_type='$property_type', location='$location', 
                         land_area='$land_area', price_range='$price_range', images='$image_path' 
                     WHERE property_id='$property_id'";

    if (mysqli_query($con, $update_query)) {
        echo "<script>alert('Property updated successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating property: " . mysqli_error($con) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Property</title>
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #0d1117;
    color: #f0f0f0;
    margin: 20px;
    padding: 20px;
}

.container {
    background: #161b22;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
    max-width: 600px;
    margin: auto;
}

h2 {
    text-align: center;
    color: #00bcd4;
    margin-bottom: 20px;
}

label {
    font-weight: bold;
    display: block;
    margin-top: 15px;
    color: #ffffff;
}

input[type="text"],
input[type="email"],
input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    background: #0d1117;
    border: 1px solid #444;
    color: #ffffff;
    border-radius: 5px;
    outline: none;
    transition: border-color 0.2s;
}

input[type="text"]:focus,
input[type="email"]:focus {
    border-color: #00bcd4;
}

.btn-container {
    text-align: center;
    margin-top: 25px;
}

.btn {
    padding: 10px 18px;
    text-decoration: none;
    color: white;
    border-radius: 5px;
    margin: 5px;
    display: inline-block;
    font-weight: bold;
    text-transform: uppercase;
    border: none;
    cursor: pointer;
}

.btn-update {
    background: #28a745;
}

.btn-update:hover {
    background: #218838;
}

.btn-back {
    background: #007BFF;
}

.btn-back:hover {
    background: #0056b3;
}

.preview-img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    display: block;
    margin: 15px auto;
    border: 1px solid #444;
    border-radius: 5px;
}

a {
    color: #00bcd4;
    text-decoration: underline;
}

a:hover {
    color: #0288d1;
}

p {
    font-size: 14px;
    color: #ccc;
    text-align: center;
}

    </style>
</head>
<body>

<div class="container">
    <h2>Edit Property</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Owner Name:</label>
        <input type="text" name="owner_name" value="<?php echo htmlspecialchars($property['owner_name']); ?>" required>

        <label>Phone:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($property['phone']); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($property['email']); ?>" required>

        <label>Property Type:</label>
        <input type="text" name="property_type" value="<?php echo htmlspecialchars($property['property_type']); ?>" required>

        <label>Location:</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($property['location']); ?>" required>

        <label>Land Area (sqm):</label>
        <input type="text" name="land_area" value="<?php echo htmlspecialchars($property['land_area']); ?>" required>

        <label>Price Range:</label>
        <input type="text" name="price_range" value="<?php echo htmlspecialchars($property['price_range']); ?>" required>


        <div class="btn-container">
            <button type="submit" class="btn btn-update">Update Property</button>
            <a href="admin_dashboard.php" class="btn btn-back">Back to Dashboard</a>
        </div>
    </form>
</div>

</body>
</html>
