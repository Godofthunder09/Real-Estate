<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

if (isset($_GET['property_id']) && isset($_GET['type'])) {
    $property_id = mysqli_real_escape_string($con, $_GET['property_id']);
    $property_type = ($_GET['type'] === 'rent') ? 'rent' : 'sale'; // default to sale
    $user_id = $_SESSION['user_id'];

    // Check if already in wishlist
    $check = mysqli_query($con, "SELECT * FROM wishlist WHERE user_id='$user_id' AND property_id='$property_id' AND property_type='$property_type'");
    
    if (mysqli_num_rows($check) == 0) {
        $sql = "INSERT INTO wishlist(user_id, property_id, property_type) VALUES('$user_id', '$property_id', '$property_type')";
        if (mysqli_query($con, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Property added to wishlist successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add property to wishlist.']);
        }
    } else {
        echo json_encode(['status' => 'info', 'message' => 'Property is already in your wishlist.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
