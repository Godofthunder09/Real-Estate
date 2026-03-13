<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = $_POST['input']; // Can be either username or email
    $password = md5($_POST['password']); // Encrypt password to match DB

    // Check if input is email or username
    $query = "SELECT * FROM admin_activity WHERE (UserName='$input' OR Email='$input') AND Password='$password'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        $_SESSION['admin'] = $admin['UserName'];
        $_SESSION['admin_email'] = $admin['Email'];
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid login details'); window.location.href='admin_login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #cfd8dc; /* Light gray */
            height: 100vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            font-family: Arial, sans-serif;
        }

        .background-images {
            position: absolute;
            top: 0;
            left: 0;
            display: flex;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .bg-img {
            flex: 1;
            background-size: cover;
            background-position: center;
            filter: grayscale(100%) brightness(0.8);
            transition: all 1s ease-in-out;
        }

        .login-container {
            background-color: #eceff1; /* Light gray */
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.25), 0 0 40px rgba(0, 0, 0, 0.1);
            border: 2px solid #b0bec5; /* Medium gray border */
            width: 320px;
            text-align: center;
            z-index: 1;
        }

        .flip-heading {
            display: inline-block;
            transition: transform 0.6s;
            transform-style: preserve-3d;
            cursor: pointer;
            font-size: 28px;
            font-weight: bold;
            color: #37474f; /* Dark gray */
        }

        .flip-heading.flipped {
            transform: rotateY(180deg);
        }

        .flip-heading span {
            display: block;
            backface-visibility: hidden;
        }

        .flip-heading span.back {
            position: absolute;
            top: 0;
            left: 0;
            transform: rotateY(180deg);
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            color: #455a64; /* Darker gray */
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #90a4ae; /* Gray border */
            border-radius: 5px;
            background-color: #f5f5f5;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            border: none;
            border-radius: 5px;
            background-color: #546e7a; /* Gray-blue */
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #455a64; /* Darker gray-blue */
        }
    </style>
</head>
<body>
    <!-- Background Images -->
    <div class="background-images">
        <div class="bg-img" style="background-image: url('/jaga/landrecordsys/assets/img/register/agri1.png');"></div>
        <div class="bg-img" style="background-image: url('/jaga/landrecordsys/assets/img/register/agri2.png');"></div>
        <div class="bg-img" style="background-image: url('/jaga/landrecordsys/assets/img/register/indu1.jpg');"></div>
    </div>

    <!-- Login Box -->
    <div class="login-container">
        <h2 class="flip-heading" onclick="flipText(this)">
            <span class="front">Admin</span>
            <span class="back">Admin</span>
        </h2>
        <form method="POST" action="">
            <label>Username or Email:</label>
            <input type="text" name="input" required>
            <br>
            <label>Password:</label>
            <input type="password" name="password" required>
            <br>
            <button type="submit">Login</button>
        </form>
    </div>

    <!-- JavaScript -->
    <script>
        function flipText(el) {
            el.classList.toggle("flipped");
        }

        const bgImages = document.querySelectorAll(".bg-img");

        function rotateBackgrounds() {
            const urls = Array.from(bgImages).map(div => div.style.backgroundImage);
            urls.unshift(urls.pop()); // Rotate array right
            bgImages.forEach((div, i) => {
                div.style.backgroundImage = urls[i];
            });
        }

        setInterval(rotateBackgrounds, 4000); // Change every 4 seconds
    </script>
</body>
</html>
