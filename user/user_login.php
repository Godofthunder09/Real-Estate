<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

// Load recent emails from cookie
$recentEmails = isset($_COOKIE['recent_login_emails']) 
    ? json_decode($_COOKIE['recent_login_emails'], true) 
    : [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        if (password_verify($password, $row["password"])) {
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['user_id'] = $row['id'];

            // Update recent emails list
            $recentEmails = array_values(array_unique(array_merge([$email], $recentEmails)));
            $recentEmails = array_slice($recentEmails, 0, 3); // Keep only 3 recent emails

            // Store in cookie for 30 days
            setcookie("recent_login_emails", json_encode($recentEmails), time() + (30 * 24 * 60 * 60), "/");

            header("Location: user_dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No account found with this email!";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>User Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
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

    /* ── INPUTS ── */
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
    input[type="email"]:focus,
    input[type="password"]:focus {
        background: #1a2040;
        border-color: rgba(79, 195, 247, 0.55);
        box-shadow: 0 0 0 3px rgba(79, 195, 247, 0.08);
    }
    /* Fix browser autofill overriding background to white */
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

    /* ── REGISTER LINK ── */
    .register-link {
        display: block;
        text-align: center;
        color: #4fc3f7;
        margin-top: 16px;
        text-decoration: none;
        font-size: 13.5px;
        opacity: 0.85;
        transition: opacity 0.2s;
    }
    .register-link:hover {
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
    <div class="container">
        <h2>User Login</h2>
        <?php if (!empty($error)) : ?>
        <div class="error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" autocomplete="off">
            <input list="email-options" type="email" name="email" placeholder="Email" required />
            <datalist id="email-options">
                <?php foreach ($recentEmails as $emailOption): ?>
                    <option value="<?= htmlspecialchars($emailOption) ?>">
                <?php endforeach; ?>
            </datalist>
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit" class="btn">Login</button>
        </form>
        <a href="user_register.php" class="register-link">Don't have an account? Register</a>
    </div>
</body>
</html>