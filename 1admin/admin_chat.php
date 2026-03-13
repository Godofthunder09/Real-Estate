<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/jaga/landrecordsys/admin/includes/dbconnection.php");

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$user_id = intval($_GET['user_id']);

// Mark all as read for this user
mysqli_query($con, "UPDATE admin_messages SET is_read = 1 WHERE user_id = $user_id");

// Fetch user info
$user_info = mysqli_fetch_assoc(mysqli_query($con, "SELECT name, email FROM users WHERE id = $user_id"));

// Handle reply
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reply'])) {
    $reply = mysqli_real_escape_string($con, $_POST['reply']);
    mysqli_query($con, "INSERT INTO admin_messages (user_id, reply, is_read) VALUES ($user_id, '$reply', 1)");
    exit; // Stop page reload for AJAX
}

// Fetch all messages
$messages = mysqli_query($con, "
    SELECT message, reply, created_at
    FROM admin_messages
    WHERE user_id = $user_id
    ORDER BY created_at ASC
");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Chat with <?= htmlspecialchars($user_info['name']); ?></title>
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

        /* Sidebar styles */
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
            word-break: break-word;
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

        /* Main content area */
        .main-content {
            flex-grow: 1;
            background: #1e1e2f;
            padding: 40px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        h2 {
            color: #00bfff;
            text-align: center;
            margin-bottom: 20px;
        }

        .chat-container {
            background: #1e1e3f;
            padding: 20px;
            border-radius: 10px;
            flex-grow: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .message {
            margin-bottom: 10px;
            max-width: 70%;
            padding: 10px;
            border-radius: 8px;
            word-wrap: break-word;
        }

        .user-msg {
            background: #2b2b60;
            align-self: flex-start;
        }

        .admin-msg {
            background: #1565c0;
            align-self: flex-end;
        }

        .reply-box {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            resize: none;
            background: #2b2b60;
            color: #fff;
            border: 1px solid #444;
            font-size: 14px;
        }

        textarea::placeholder {
            color: #bbb;
        }

        button {
            background: #00bfff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            margin-top: 5px;
            cursor: pointer;
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

<!-- Main Chat Content -->
<div class="main-content">
    <h2>💬 Chat with <?= htmlspecialchars($user_info['name']); ?> (<?= htmlspecialchars($user_info['email']); ?>)</h2>
    <div class="chat-container" id="chatBox">
        <?php while ($msg = mysqli_fetch_assoc($messages)) { ?>
            <?php if (!empty($msg['reply'])) { ?>
                <div class="message admin-msg">
                    <?= nl2br(htmlspecialchars($msg['reply'])); ?>
                    <div style="font-size:10px;color:gray;"><?= $msg['created_at']; ?></div>
                </div>
            <?php } elseif (!empty($msg['message'])) { ?>
                <div class="message user-msg">
                    <?= nl2br(htmlspecialchars($msg['message'])); ?>
                    <div style="font-size:10px;color:gray;"><?= $msg['created_at']; ?></div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="reply-box">
        <form id="replyForm" method="POST">
            <textarea name="reply" rows="3" placeholder="Type your reply..."></textarea>
            <button type="submit">Send</button>
        </form>
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

// Auto-scroll to bottom
function scrollToBottom() {
    const chatBox = document.getElementById('chatBox');
    chatBox.scrollTop = chatBox.scrollHeight;
}
scrollToBottom();

// Handle AJAX send without reload
document.getElementById('replyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    }).then(() => {
        location.reload();
    });
});
</script>

</body>
</html>
