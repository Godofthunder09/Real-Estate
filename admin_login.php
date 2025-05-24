<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = $_POST['input']; // Can be either username or email
    $password = md5($_POST['password']); // Encrypt password to match DB

    $query = "SELECT * FROM admin_activity WHERE (UserName='$input' OR Email='$input') AND Password='$password'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        $_SESSION['admin'] = $admin['UserName'];
        $_SESSION['admin_email'] = $admin['Email'];
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid login details!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=display=swap');

        body {
            font-family: sans-serif;
            background-color: #121212;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #1a1a2e;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #00eaff;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            color: #00eaff;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 2px solid #00eaff;
            border-radius: 5px;
            background-color: #0f3460;
            color: white;
        }
        input:focus {
            outline: none;
            border-color: #ff9800;
        }
        button {
            background-color: #00eaff;
            color: black;
            font-weight: bold;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }
        button:hover {
            background-color: #ff9800;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        
        <?php if (!empty($error)) { ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>

        <form method="POST">
            <label>Username or Email:</label>
            <input type="text" name="input" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
