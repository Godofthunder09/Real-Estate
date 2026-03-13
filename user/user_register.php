<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // 1. Required fields
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    }
    // 2. Name length check
    elseif (strlen($name) < 3) {
        $error = "Name must be at least 3 characters long!";
    }
    // 3. Email format validation
    elseif (!preg_match('/^[A-Za-z0-9._%+-]+@gmail\.com$/', $email)) {
        $error = "Please enter a valid Gmail address (example@gmail.com)!";
    }
    // 4. Password strength check
    elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $error = "Password must be at least 8 characters, with uppercase, lowercase, and a number!";
    }
    else {
        // 5. Check for duplicate email
        $checkQuery = "SELECT id FROM users WHERE email = ?";
        $stmt = $con->prepare($checkQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "This email is already registered. Please use a different one!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("sss", $name, $email, $hashedPassword);

            if ($stmt->execute()) {
                header("Location: user_login.php?success=registered");
                exit();
            } else {
                $error = "Registration failed! Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #0a0e1f;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        /* ── MAIN PANEL ── */
        .main {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        /* ── FORM CARD ── */
        .container {
            background: rgba(18, 24, 52, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 42px 36px;
            border-radius: 14px;
            width: 100%;
            max-width: 420px;
            border: 1px solid rgba(79, 195, 247, 0.18);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4),
                        0 0 20px rgba(79, 195, 247, 0.07);
        }

        h2 {
            text-align: center;
            color: #4fc3f7;
            margin-bottom: 28px;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* ── INPUTS — keep dark bg even on focus/autofill ── */
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            margin: 9px 0;
            border: 1px solid rgba(79, 195, 247, 0.15);
            border-radius: 8px;
            background: #1a2040;
            color: #e0e6f0;
            font-size: 14px;
            transition: border-color 0.25s, box-shadow 0.25s;
            outline: none;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            background: #1a2040;
            border-color: rgba(79, 195, 247, 0.55);
            box-shadow: 0 0 0 3px rgba(79, 195, 247, 0.08);
        }
        /* Fix browser autofill white background */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #1a2040 inset !important;
            -webkit-text-fill-color: #e0e6f0 !important;
            caret-color: #e0e6f0;
        }
        input::placeholder { color: #7a8aad; }

        /* ── BUTTON ── */
        .btn {
            background: #2a2f6e;
            color: #ffffff;
            border: 1px solid rgba(79, 195, 247, 0.25);
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.4px;
            margin-top: 6px;
            transition: all 0.25s ease-in-out;
            box-shadow: 0 4px 14px rgba(42, 47, 110, 0.5);
        }
        .btn:hover {
            background: #353c88;
            border-color: rgba(79, 195, 247, 0.5);
            box-shadow: 0 0 14px rgba(79, 195, 247, 0.2);
        }
        .btn:active {
            transform: scale(0.97);
        }

        /* ── LOGIN LINK ── */
        .login-link {
            display: block;
            text-align: center;
            color: #4fc3f7;
            margin-top: 16px;
            text-decoration: none;
            font-size: 13.5px;
            opacity: 0.85;
            transition: opacity 0.2s;
        }
        .login-link:hover {
            opacity: 1;
            text-decoration: underline;
        }

        /* ── ERROR ── */
        .error {
            background: rgba(255, 82, 82, 0.15);
            border: 1px solid rgba(255, 82, 82, 0.4);
            padding: 10px 14px;
            color: #ff6b6b;
            border-radius: 8px;
            margin-bottom: 16px;
            text-align: center;
            font-size: 13.5px;
        }
    </style>
</head>
<body>

<div class="main">
    <div class="container">
        <h2>Create Account</h2>
        <?php if (!empty($error)) { ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Register</button>
        </form>
        <a href="user_login.php" class="login-link">Already have an account? Login</a>
    </div>
</div>

</body>
</html>