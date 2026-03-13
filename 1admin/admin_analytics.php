<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Get property type distribution
$query1 = "SELECT property_type, COUNT(*) as count FROM addproperty GROUP BY property_type";
$result1 = mysqli_query($con, $query1);
$propertyTypes = [];
$propertyCounts = [];

while ($row = mysqli_fetch_assoc($result1)) {
    $propertyTypes[] = $row['property_type'];
    $propertyCounts[] = $row['count'];
}

// Get user uploads
$query2 = "SELECT email, COUNT(*) as uploads FROM addproperty GROUP BY email ORDER BY uploads DESC LIMIT 10";
$result2 = mysqli_query($con, $query2);
$userEmails = [];
$userUploads = [];

while ($row = mysqli_fetch_assoc($result2)) {
    $userEmails[] = $row['email'];
    $userUploads[] = $row['uploads'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #121212;
            color: #fff;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(to bottom right, #14213d, #1a2a6c);
            padding: 30px 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
            flex-shrink: 0;
        }

        .sidebar h2 {
            color: #9be1ff;
            margin-bottom: 10px;
            font-size: 20px;
            text-align: center;
        }

        .sidebar p {
            color: #cfd8dc;
            font-size: 14px;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar a, .sidebar .submenu-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            margin-bottom: 15px;
            text-decoration: none;
            color: white;
            border-radius: 8px;
            background-color: #2c2c54;
            transition: background 0.3s ease, transform 0.2s ease;
            font-weight: bold;
            cursor: pointer;
        }

        .sidebar a.logout {
            background-color: #2c2c54;
        }

        .sidebar a.logout:hover {
            background-color: #b71c1c !important;
            transform: scale(1.05);
        }

        .sidebar a:hover, .sidebar .submenu-toggle:hover {
            background-color: #1565c0;
            transform: scale(1.05);
        }

        .submenu-container {
            margin-bottom: 10px;
        }

        .submenu {
            display: none;
            flex-direction: column;
            margin-left: 15px;
        }

        .submenu a {
            background-color: #1a1a2f;
            font-size: 14px;
        }

        .submenu a:hover {
            background-color: #1565c0;
        }

        .triangle {
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 8px solid white;
            transition: transform 0.3s ease;
        }

        .triangle.open {
            transform: rotate(180deg);
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            background: #1e1e2f;
            padding: 40px;
            overflow-y: auto;
        }

        .chart-container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
            background-color: #2e2e3f;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 255, 255, 0.2);
        }

        canvas {
            background-color: #fff;
            border-radius: 10px;
            padding: 10px;
        }

        h3 {
            text-align: center;
            color: #00bfff;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Welcome Admin 👨‍💼</h2>
    <p>Manage the system effectively</p>

    <a href="/jaga/landrecordsys/1admin/admin_users.php">👤 User Details</a>

    <div class="submenu-container">
        <div class="submenu-toggle">
            ➕ Sell/Rent Property
            <div class="triangle"></div>
        </div>
        <div class="submenu">
            <a href="/jaga/landrecordsys/1admin/add_property.php?type=sell">Sell Property</a>
            <a href="/jaga/landrecordsys/1admin/add_renta_property.php?type=rent">Rent Property</a>
        </div>
    </div>

    <div class="submenu-container">
        <div class="submenu-toggle">
            🏠 View Properties
            <div class="triangle"></div>
        </div>
        <div class="submenu">
            <a href="/jaga/landrecordsys/1admin/admin_properties.php?filter=buy">Buy Properties</a>
            <a href="/jaga/landrecordsys/1admin/admin_renta_properties.php?filter=rent">Rent Properties</a>
        </div>
    </div>

    <a href="/jaga/landrecordsys/1admin/admin_analytics.php">📊 Analytics Dashboard</a>
    <a href="/jaga/landrecordsys/1admin/admin_messages.php">📥 MSG</a>
    <a href="admin_logout.php" class="logout">🚪 Logout</a>
</div>

<!-- Main Content -->
<!-- Main Content -->
<div class="main-content">
    <!-- Rent Button -->
    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <a href="/jaga/landrecordsys/1admin/admin_rent_analytics.php" 
           style="background-color: #00bfff; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: 0.3s;">
            For Rent
        </a>
    </div>

    <div class="chart-container">
        <h3>Property Types (Pie Chart)</h3>
        <canvas id="propertyPieChart"></canvas>
    </div>

    <div class="chart-container">
        <h3>Top Uploading Users (Bar Graph)</h3>
        <canvas id="userBarChart"></canvas>
    </div>
</div>

<script>
    // Submenu toggle functionality
    const toggles = document.querySelectorAll('.submenu-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            const submenu = toggle.nextElementSibling;
            const triangle = toggle.querySelector('.triangle');
            submenu.style.display = submenu.style.display === 'flex' ? 'none' : 'flex';
            triangle.classList.toggle('open');
        });
    });

    // Pie Chart - Property Types
    const propertyPie = document.getElementById('propertyPieChart').getContext('2d');
    new Chart(propertyPie, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($propertyTypes); ?>,
            datasets: [{
                data: <?php echo json_encode($propertyCounts); ?>,
                backgroundColor: ['#4CAF50', '#2196F3', '#FFC107', '#FF5722', '#9C27B0'],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: { color: '#000' }
                }
            }
        }
    });

    // Bar Chart - User Uploads
    const userBar = document.getElementById('userBarChart').getContext('2d');
    new Chart(userBar, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($userEmails); ?>,
            datasets: [{
                label: 'Properties Uploaded',
                data: <?php echo json_encode($userUploads); ?>,
                backgroundColor: '#00bcd4',
                borderRadius: 10
            }]
        },
        options: {
            scales: {
                x: { ticks: { color: '#000' } },
                y: { beginAtZero: true, ticks: { color: '#000' } }
            },
            plugins: {
                legend: { labels: { color: '#000' } }
            }
        }
    });
</script>

</body>
</html>
