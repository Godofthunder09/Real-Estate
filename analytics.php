<?php
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

// Fetch total number of users
$userCountQuery = "SELECT COUNT(*) AS total_users FROM users";
$userCountResult = mysqli_query($con, $userCountQuery);
$userCount = mysqli_fetch_assoc($userCountResult)['total_users'];

// Fetch total number of properties
$propertyCountQuery = "SELECT COUNT(*) AS total_properties FROM addproperty";
$propertyCountResult = mysqli_query($con, $propertyCountQuery);
$propertyCount = mysqli_fetch_assoc($propertyCountResult)['total_properties'];

// Fetch property count by type
$propertyTypeQuery = "SELECT property_type, COUNT(*) AS count FROM addproperty GROUP BY property_type";
$propertyTypeResult = mysqli_query($con, $propertyTypeQuery);
$propertyTypeData = [];
while ($row = mysqli_fetch_assoc($propertyTypeResult)) {
    $propertyTypeData[] = [$row['property_type'], (int)$row['count']];
}

// Fetch top users with most properties
$topUsersQuery = "SELECT users.name, COUNT(addproperty.property_id) AS total_properties 
                  FROM users 
                  JOIN addproperty ON users.id = addproperty.user_id 
                  GROUP BY users.name 
                  ORDER BY total_properties DESC 
                  LIMIT 5";
$topUsersResult = mysqli_query($con, $topUsersQuery);
$topUsersData = [];
while ($row = mysqli_fetch_assoc($topUsersResult)) {
    $topUsersData[] = [$row['name'], (int)$row['total_properties']];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Website Analytics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:rgb(10, 10, 10);
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 10px gray;
        }
        .navbar {
            background: #007BFF;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        h2 {
            text-align: center;
        }
        .report-box {
            padding: 20px;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0px 0px 5px gray;
            text-align: center;
        }
        .blue { background: #007BFF; color: white; }
        .green { background: #28a745; color: white; }
        .orange { background: #FFA500; color: white; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background: #007BFF;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <h2>Website Analytics</h2>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="visualization.php">View Data Visualization</a>
    </div>

    <div class="container">
        <h2>Website Analytics</h2>

        <!-- Total Users -->
        <div class="report-box blue">
            <h3>Total Users</h3>
            <p><strong><?php echo $userCount; ?></strong></p>
        </div>

        <!-- Total Properties -->
        <div class="report-box green">
            <h3>Total Properties Listed</h3>
            <p><strong><?php echo $propertyCount; ?></strong></p>
        </div>

        <!-- Properties by Type -->
        <div class="report-box orange">
            <h3>Properties by Type</h3>
            <table>
                <tr>
                    <th>Property Type</th>
                    <th>Count</th>
                </tr>
                <?php foreach ($propertyTypeData as $data) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($data[0]); ?></td>
                    <td><?php echo htmlspecialchars($data[1]); ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <!-- Top Users with Most Properties -->
        <div class="report-box blue">
            <h3>Top 5 Users with Most Properties</h3>
            <table>
                <tr>
                    <th>User Name</th>
                    <th>Properties Listed</th>
                </tr>
                <?php foreach ($topUsersData as $data) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($data[0]); ?></td>
                    <td><?php echo htmlspecialchars($data[1]); ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>
