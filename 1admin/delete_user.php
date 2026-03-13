<?php
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Delete user from the database
    $deleteQuery = "DELETE FROM users WHERE id = $id";
    if (mysqli_query($con, $deleteQuery)) {
        echo "<script>alert('User deleted successfully!'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting user!'); window.location='admin_dashboard.php';</script>";
    }
}
?>
