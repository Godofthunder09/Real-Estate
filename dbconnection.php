<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Default for XAMPP/WAMP
$password = ""; // Default is empty
$database = "lrsdb";

// Establish connection
$con = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$con) {
    die("Connection Failed: " . mysqli_connect_error());
} else {
    // Uncomment the line below to confirm connection (for debugging)
    // echo "Database Connected Successfully!";
}
?>
