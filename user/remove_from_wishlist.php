<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$property_id = $_GET['property_id'] ?? '';

if (!empty($property_id)) {
    mysqli_query($con, "DELETE FROM wishlist WHERE user_id = $user_id AND property_id = '$property_id'");
}

header("Location: wishlist.php");
exit();
?>
