<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

// Ensure admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all users
$userQuery = "SELECT * FROM users"; 
$userResult = mysqli_query($con, $userQuery);

// Fetch all properties
$query = "SELECT * FROM addproperty ORDER BY created_at DESC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #000; /* Black Background */
    color: #fff; /* White Text */
}

.container {
    width: 90%;
    margin: 20px auto;
    background: #1e1e1e; /* Dark Gray Background */
    padding: 20px;
    box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.2);
    border-radius: 10px;
}

.navbar {
    background: #007BFF;
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #4DB6AC;
}

.navbar a {
    color: white;
    text-decoration: none;
    font-weight: bold;
}

.btn-container {
    margin: 20px 0;
    text-align: center;
}

.btn {
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    margin: 10px;
    font-size: 16px;
    border-radius: 5px;
    color: white;
}

.btn-user { background: #28a745; }
.btn-property { background: #17a2b8; }
.btn-add-property { background: #FFA500; }
.btn-report { background: #6c757d; }
.btn-logout { background: #dc3545; }

.section {
    display: none;
    margin: 20px 0;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #222; /* Dark Table Background */
    color: #fff;
}

table, th, td {
    border: 1px solid #555;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background: #007BFF;
    color: white;
}

.delete-btn {
    background: red;
    color: white;
    padding: 5px 10px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

.btn {
    display: inline-block;
    padding: 8px 14px;
    font-size: 14px;
    font-weight: bold;
    text-decoration: none;
    border-radius: 5px;
    transition: 0.3s;
    margin: 2px;
}

.btn-view { background-color: #007BFF; color: white; border: 1px solid #007BFF; }
.btn-edit { background-color: #28a745; color: white; border: 1px solid #28a745; }
.btn-delete { background-color: #dc3545; color: white; border: 1px solid #dc3545; }

.btn:hover { opacity: 0.8; }

    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <h2>Admin Dashboard</h2>
        <a href="admin_logout.php">Logout</a>
    </div>

    <!-- Buttons to Show Sections -->
<div class="btn-container">
    <button class="btn btn-user" onclick="showSection('userDetails')">User Details</button>
    <button class="btn btn-property" onclick="showSection('propertyDetails')">Property Details</button>
    
    <a href="/jaga/landrecordsys/1admin/analytics.php">
        <button class="btn btn-report" style="background: #6c757d;">View Reports</button>
    </a>
</div>

        <!-- User Details Section -->
        <div id="userDetails" class="section">
            <h3>User Details</h3>
            <table>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
                <?php while ($userRow = mysqli_fetch_assoc($userResult)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($userRow['id']); ?></td>
                    <td><?php echo htmlspecialchars($userRow['name']); ?></td>
                    <td><?php echo htmlspecialchars($userRow['email']); ?></td>
                    <td>
                        <a href="delete_user.php?id=<?php echo $userRow['id']; ?>" onclick="return confirm('Are you sure?');">
                            <button class="delete-btn">Delete</button>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <!-- Property Details Section -->
        <div id="propertyDetails" class="section">
            <h3>Property Listings</h3>
            <table>
                <tr>
                    <th>Property ID</th>
                    <th>Owner Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Property Type</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['property_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['property_type']); ?></td>
                    <td style="text-align: center;">
                        <a href="view_property.php?id=<?php echo $row['property_id']; ?>" class="btn btn-view">View</a>
                        <a href="edit_property.php?id=<?php echo $row['property_id']; ?>" class="btn btn-edit">Edit</a>
                        <a href="delete_property.php?id=<?php echo $row['property_id']; ?>" class="btn btn-delete" 
                           onclick="return confirm('Are you sure you want to delete this property?');">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <script>
        function showSection(id) {
            document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
            document.getElementById(id).style.display = 'block';
        }
        document.addEventListener("DOMContentLoaded", function() {
            showSection('userDetails');
        });
    </script>
</body>
</html>
