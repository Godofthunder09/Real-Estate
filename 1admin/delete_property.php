<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_GET['id'])) {
    echo "Property ID is missing.";
    exit();
}

$property_id = $_GET['id'];

$delete_query = "DELETE FROM addproperty WHERE property_id='$property_id'";

if (mysqli_query($con, $delete_query)) {
    echo "<script>alert('Property deleted successfully!'); window.location.href='admin_dashboard.php';</script>";
} else {
    echo "Error deleting property: " . mysqli_error($con);
}
?>
