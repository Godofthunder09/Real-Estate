<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message_sent = "";

// Handle new message submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $message = mysqli_real_escape_string($con, $_POST['message']);
    if (!empty($message)) {
        $query = "INSERT INTO admin_messages (user_id, message, is_read, created_at) 
                  VALUES ('$user_id', '$message', 0, NOW())";
        if (mysqli_query($con, $query)) {
            $_SESSION['message_sent'] = "✅ Message sent to admin successfully.";
            header("Location: contact_admin.php");
            exit();
        } else {
            $message_sent = "❌ Error sending message: " . mysqli_error($con);
        }
    } else {
        $message_sent = "⚠ Please enter a message.";
    }
}

// Retrieve flash message from session
if (isset($_SESSION['message_sent'])) {
    $message_sent = $_SESSION['message_sent'];
    unset($_SESSION['message_sent']);
}

// Fetch messages
$msg_query = "SELECT message, reply, is_read, created_at 
              FROM admin_messages 
              WHERE user_id='$user_id' 
              ORDER BY created_at ASC";
$msg_result = mysqli_query($con, $msg_query);

// Check for unread admin replies for badge
$notif_query = "SELECT COUNT(*) AS unread_count 
                FROM admin_messages 
                WHERE user_id='$user_id' AND reply != '' AND is_read = 0";
$notif_result = mysqli_query($con, $notif_query);
$unread_count = mysqli_fetch_assoc($notif_result)['unread_count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Contact Admin</title>
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
.sidebar p { color: #cfd8dc; font-size: 14px; margin-bottom: 30px; text-align:center; }
.sidebar a, .submenu-toggle {
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
.sidebar a:hover, .submenu-toggle:hover { background-color: #1565c0; transform: scale(1.05); }
.sidebar a.logout { background-color: #2c2c54; }
.sidebar a.logout:hover { background-color: #b71c1c !important; transform: scale(1.05); }

/* Submenu */
.submenu { display: none; flex-direction: column; margin-left: 15px; }
.submenu a { background-color: #1a1a2f; font-size: 14px; }
.submenu a:hover { background-color: #1565c0; }

.triangle {
    width: 0; height: 0;
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-top: 8px solid white;
    transition: transform 0.3s ease;
}
.triangle.open { transform: rotate(180deg); }

/* Main Content */
.main-content {
    flex-grow: 1;
    background: #1e1e2f;
    display: flex;
    flex-direction: column;
    padding: 20px;
    overflow-y: auto;
}

/* Flash Message */
.flash-message {
    padding: 10px;
    background: #0b3d91;
    color: #fff;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
}

/* Chat History */
.chat-history {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
    overflow-y: auto;
    padding: 20px;
    background: #141432;
    border-radius: 10px;
    margin-bottom: 15px;
}
.chat-message { padding: 10px 15px; border-radius: 10px; max-width: 70%; word-wrap: break-word; font-size: 14px; }
.user-message { align-self: flex-end; background: #1565c0; color: white; }
.admin-message { align-self: flex-start; background: #2a2a4a; color: white; }
.timestamp { font-size: 11px; opacity: 0.7; margin-top: 4px; display: block; }

/* Message Form */
.message-form { display: flex; flex-direction: column; }
.message-form textarea {
    width: 100%; padding: 10px; border-radius: 6px; resize: none; background: #2b2b60; color: #fff; border:1px solid #444; font-size:14px; margin-bottom:8px;
}
.message-form textarea::placeholder { color: #bbb; }
.message-form button { background:#00bfff;color:white;padding:10px;border:none;border-radius:6px;cursor:pointer;font-weight:bold;width:fit-content; }
.message-form button:hover { background:#0097cc; }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?> 👋</h2>
    <p>Manage your properties easily.</p>

    <!-- Sell/Rent submenu -->
    <div class="submenu-toggle">➕ Sell/Rent Property <div class="triangle"></div></div>
    <div class="submenu">
        <a href="add_property.php">Sell Property</a>
        <a href="add_rent_property.php">Rent Property</a>
    </div>

    <!-- View Properties submenu -->
    <div class="submenu-toggle">🏠 View Properties <div class="triangle"></div></div>
    <div class="submenu">
        <a href="view_properties.php">Buy Properties</a>
        <a href="view_rent_properties.php">Rent Properties</a>
    </div>

    <a href="search_property.php">🔍 Search Property</a>
    <a href="wishlist.php">💙 Wishlist</a>
    <a href="contact_admin.php">
        📩 MSG Admin
        <?php if ($unread_count > 0): ?>
            <span style="background:red;color:white;font-size:12px;padding:2px 6px;border-radius:10px;"><?php echo $unread_count; ?></span>
        <?php endif; ?>
    </a>
    <a href="user_logout.php" class="logout">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <?php if ($message_sent): ?>
        <div class="flash-message"><?php echo $message_sent; ?></div>
    <?php endif; ?>

    <div class="chat-history" id="chatHistory">
        <?php
        if (mysqli_num_rows($msg_result) > 0) {
            while ($row = mysqli_fetch_assoc($msg_result)) {
                if (!empty(trim($row['message']))) {
                    echo "<div class='chat-message user-message'>";
                    echo htmlspecialchars($row['message']);
                    echo "<span class='timestamp'>" . date("Y-m-d H:i:s", strtotime($row['created_at'])) . "</span>";
                    echo "</div>";
                }
                if (!empty(trim($row['reply']))) {
                    echo "<div class='chat-message admin-message'>";
                    echo htmlspecialchars($row['reply']);
                    echo "<span class='timestamp'>" . date("Y-m-d H:i:s", strtotime($row['created_at'])) . "</span>";
                    echo "</div>";
                }
            }
        } else {
            echo "<p>No messages yet.</p>";
        }
        ?>
    </div>

    <form method="POST" class="message-form">
        <textarea name="message" rows="3" placeholder="Type your message..." required></textarea>
        <button type="submit">Send</button>
    </form>
</div>

<script>
const toggles = document.querySelectorAll('.submenu-toggle');
toggles.forEach(toggle => {
    toggle.addEventListener('click', () => {
        const submenu = toggle.nextElementSibling;
        const triangle = toggle.querySelector('.triangle');
        submenu.style.display = submenu.style.display === 'flex' ? 'none' : 'flex';
        triangle.classList.toggle('open');
    });
});

// Scroll chat to bottom
const chatHistory = document.getElementById('chatHistory');
chatHistory.scrollTop = chatHistory.scrollHeight;
</script>

</body>
</html>
