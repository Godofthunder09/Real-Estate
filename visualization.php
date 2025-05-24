<?php
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

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
    <title>Data Visualization</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #000; /* Black Background */
    color: #fff; /* White Text */
}

.container {
    width: 80%;
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

h2 {
    text-align: center;
    color: #fff; /* White Text */
}

#propertyTypeChart, #topUsersChart {
    background: #222; /* Dark Chart Background */
    padding: 10px;
    border-radius: 8px;
}

    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <h2>Data Visualization</h2>
        <a href="analytics.php">View Website Analytics</a>
    </div>

    <div class="container">
        <h2>Data Visualization</h2>

        <!-- Charts -->
        <div id="propertyTypeChart" style="width: 100%; height: 400px;"></div>
        <div id="topUsersChart" style="width: 100%; height: 400px;"></div>
    </div>

    <!-- Google Charts Script -->
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawCharts);

        function drawCharts() {
            var propertyTypeData = google.visualization.arrayToDataTable([
                ['Property Type', 'Count'],
                <?php foreach ($propertyTypeData as $data) {
                    echo "['" . $data[0] . "', " . $data[1] . "],";
                } ?>
            ]);

            var propertyTypeChart = new google.visualization.PieChart(document.getElementById('propertyTypeChart'));
            propertyTypeChart.draw(propertyTypeData, {title: 'Property Type Distribution', pieHole: 0.4, colors: ['#FF5733', '#33FF57', '#3357FF', '#FF33A8']});

            var topUsersData = google.visualization.arrayToDataTable([
                ['User Name', 'Properties Listed'],
                <?php foreach ($topUsersData as $data) {
                    echo "['" . $data[0] . "', " . $data[1] . "],";
                } ?>
            ]);

            var topUsersChart = new google.visualization.BarChart(document.getElementById('topUsersChart'));
            topUsersChart.draw(topUsersData, {
                title: 'Top 5 Users with Most Properties', 
                bars: 'horizontal', 
                colors: ['#007BFF'],
                hAxis: {title: 'Number of Properties'},
                vAxis: {title: 'User Name'}
            });
        }
    </script>
</body>
</html>
