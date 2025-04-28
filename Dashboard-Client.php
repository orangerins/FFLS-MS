<?php
session_start();
include 'db_connection.php';

// Ensure client is logged in
if (!isset($_SESSION['client_logged_in'], $_SESSION['client_username'])) {
    echo "<script>alert('Please log in to view your dashboard.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch user details
$clientUsername = mysqli_real_escape_string($conn, $_SESSION['client_username']);
$sqlUser = "SELECT first_name, last_name, email, street FROM users 
            JOIN user_account ON users.user_id = user_account.user_account_id 
            WHERE user_account.username='$clientUsername'";
$resultUser = $conn->query($sqlUser);
$user = $resultUser->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FreshFold Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
            text-decoration: none;
        }

        body {
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        header {
            background: #82b8ef;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            height: 60px;
        }

        .logo-menu {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #menu-btn {
            background: none;
            border: none;
            cursor: pointer;
        }

        #menu-btn img {
            width: 25px;
            height: 25px;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            position: relative;
        }

        .user-dropdown span {
            margin-left: 10px;
            font-weight: bold;
        }

        .user-dropdown img {
            width: 20px; /* Adjusted icon size */
            height: 20px;
        }

        .logout-box {
            position: absolute;
            top: 30px;
            right: 0;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: none;
            z-index: 1001;
        }

        .logout-box a {
            display: block;
            padding: 10px 20px;
            color: #165a91;
            text-decoration: none;
            font-size: 14px;
        }

        .logout-box a:hover {
            background-color: #f0f0f0;
        }

        .sidebar {
            background: #96c7f9;
            width: 240px;
            height: 100vh;
            position: fixed;
            left: -240px;
            top: 60px;
            padding-top: 10px;
            border-right: 1px solid #ccc;
            transition: left 0.3s ease-in-out;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
        }

        .sidebar ul li img {
            width: 24px;
            height: 24px;
        }

        .sidebar ul li a {
            color: white;
            font-size: 16px;
        }

        .content {
            margin-top: 70px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            transition: margin-left 0.3s ease-in-out;
        }

        .content.shift {
            margin-left: 260px;
        }

        .user-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .user-info h2 {
            margin-bottom: 15px;
            color: #333;
        }

        .user-info p {
            margin: 5px 0;
            color: #555;
        }

        .edit-btn {
            background-color: #82b8ef;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .recent-orders {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .recent-orders h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #eee;
            border-radius: 5px;
        }

        .order-item span {
            color: #555;
        }

        .edit-form {
            display: none;
            flex-direction: column;
            gap: 10px;
        }

        .edit-form input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .save-btn {
            background-color: #82b8ef;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<header>
    <div class="logo-menu">
        <img src="FFLSlogo.png" alt="Logo" style="height:50px;">
        <button id="menu-btn"><img src="m-icon.png" alt="Menu"></button>
    </div>
    <div class="user-dropdown" id="userDropdown">
        <span><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
        <img src="ad-icon.png" alt="Dropdown Icon">
        <div class="logout-box" id="logoutBox">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</header>

<div class="sidebar" id="sidebar">
    <ul>
        <li><img src="d-icon.png" alt="Dashboard"><a href="Dashboard-Client.php">Dashboard</a></li>
        <li><img src="O-icon.png" alt="Orders"><a href="Orders-Client.php">Orders</a></li>
        <li><img src="p-icon.png" alt="Payments"><a href="Payments-Client.php">Payments</a></li>
        <li><img src="OT-icon.png" alt="Order Tracking"><a href="Order_Tracking-Client.php">Order Tracking</a></li>
    </ul>
</div>

<div class="content" id="mainContent">
    <div class="user-info">
        <h2>User Information</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($clientUsername); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($user['street']); ?></p>
        <button class="edit-btn" id="editBtn">Edit Information</button>
        <form class="edit-form" id="editForm" action="update_profile.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($clientUsername); ?>" required>
            <input type="text" name="address" placeholder="Address" value="<?php echo htmlspecialchars($user['street']); ?>" required>
            <button type="submit" class="save-btn">Save Changes</button>
        </form>
    </div>

    <div class="recent-orders">
        <h3>Recent Orders</h3>
        <div class="order-item"><span>Order #12345</span><span>Status: Completed</span></div>
        <div class="order-item"><span>Order #12346</span><span>Status: In Progress</span></div>
        <div class="order-item"><span>Order #12347</span><span>Status: Pending</span></div>
    </div>
</div>

<script>
    const menuBtn = document.getElementById('menu-btn');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const userDropdown = document.getElementById('userDropdown');
    const logoutBox = document.getElementById('logoutBox');
    const editBtn = document.getElementById('editBtn');
    const editForm = document.getElementById('editForm');

    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        mainContent.classList.toggle('shift');
    });

    userDropdown.addEventListener('click', () => {
        logoutBox.style.display = logoutBox.style.display === 'block' ? 'none' : 'block';
    });
    document.addEventListener('click', (e) => {
        if (!userDropdown.contains(e.target)) {
            logoutBox.style.display = 'none';
        }
    });

    editBtn.addEventListener('click', () => {
        editForm.style.display = 'flex';
        editBtn.style.display = 'none';
    });
</script>

</body>
</html>