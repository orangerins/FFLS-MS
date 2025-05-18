<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
$adminUsername = $_SESSION['admin_username'];

include('db_connection.php');

$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM laundry_request")->fetch_assoc()['total'] ?? 0;

$pendingOrders = $conn->query("SELECT COUNT(*) AS pending FROM laundry_request WHERE status = 'Pending'")->fetch_assoc()['pending'] ?? 0;

$totalSales = $conn->query("SELECT SUM(total_price) AS total_sales FROM laundry_request")->fetch_assoc()['total_sales'] ?? 0;

$totalCustomers = $conn->query("SELECT COUNT(*) AS total FROM customer")->fetch_assoc()['total'] ?? 0;

$recentOrders = [];
$sqlRecent = "
    SELECT lr.laundry_request_id, c.first_name, c.last_name, lr.date_placed, lr.status
    FROM laundry_request lr
    JOIN customer c ON lr.customer_id = c.customer_id
    ORDER BY lr.date_placed DESC
    LIMIT 5";
$resultRecent = $conn->query($sqlRecent);
if ($resultRecent) {
    while ($row = $resultRecent->fetch_assoc()) {
        $recentOrders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FreshFold Laundry Services</title>
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

        .user-profile {
            display: flex;
            align-items: center;
            gap: 5px;
            position: relative;
        }

        .user-profile img {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .user-profile span {
            font-size: 14px;
        }

        .sidebar {
            background: #96c7f9;
            width: 240px;
            height: 100vh;
            position: fixed;
            left: -240px;
            top: 55px;
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
            margin-left: 20px;
            margin-top: 60px;
            padding: 10px;
            transition: margin-left 0.3s ease-in-out;
        }

        .content.shift {
            margin-left: 260px;
        }

        .dashboard-welcome {
            background: #82b8ef;
            color: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .statistics {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .statistics .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            width: 200px;
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .statistics .stat-card h3 {
            margin-bottom: 10px;
        }

        .statistics .stat-card p {
            font-size: 20px;
            font-weight: bold;
        }

        .orders-summary {
            margin-top: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }

        th {
            background-color: #96c7f9;
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
    </style>
</head>
<body>
<header>
    <div class="logo-menu">
        <img src="FFLSlogo.png" alt="FreshFold Logo" style="height:50px;">
        <button id="menu-btn"><img src="m-icon.png" alt="Menu"></button>
    </div>
    <div class="user-profile" id="user-profile">
        <span><?= htmlspecialchars($adminUsername) ?></span>
        <img src="ad-icon.png" alt="User Icon">
        <div class="logout-box" id="logout-box">
            <a href="admin_login.php">Logout</a>
        </div>
    </div>
</header>

<div class="sidebar" id="sidebar">
    <ul>
        <li><img src="d-icon.png"><a href="admin-dashboard.php">Dashboard</a></li>
        <li><img src="O-icon.png"><a href="admin-orders.php">Orders</a></li>
        <li><img src="c-icon.png"><a href="admin-customers.php">Customers</a></li>
        <li><img src="i-icon.png"><a href="admin-inventory.php">Inventory</a></li>
        <li><img src="p-icon.png"><a href="admin-payments.php">Payments</a></li>
    </ul>
</div>

<div class="content" id="content">
    <div class="dashboard-welcome">
        <h2>Welcome, <?= htmlspecialchars($adminUsername) ?>!</h2>
        <p>Manage all orders and view statistics below.</p>
    </div>

    <div class="statistics">
        <div class="stat-card"><h3>Total Orders</h3><p><?= $totalOrders ?></p></div>
        <div class="stat-card"><h3>Pending Orders</h3><p><?= $pendingOrders ?></p></div>
        <div class="stat-card"><h3>Total Sales</h3><p>₱<?= number_format($totalSales, 2) ?></p></div>
        <div class="stat-card"><h3>Total Customers</h3><p><?= $totalCustomers ?></p></div>
    </div>

    <div class="orders-summary">
        <h3>Recent Orders</h3>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date Placed</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($recentOrders) > 0): ?>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($order['laundry_request_id']) ?></td>
                            <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                            <td><?= htmlspecialchars($order['date_placed']) ?></td>
                            <td><?= htmlspecialchars($order['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No recent orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const menuBtn = document.getElementById('menu-btn');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const userProfile = document.getElementById('user-profile');
    const logoutBox = document.getElementById('logout-box');

    menuBtn.onclick = () => {
        sidebar.classList.toggle('active');
        content.classList.toggle('shift');
    };

    userProfile.onclick = (e) => {
        logoutBox.style.display = logoutBox.style.display === 'block' ? 'none' : 'block';
        e.stopPropagation();
    };

    document.addEventListener('click', (e) => {
        if (!userProfile.contains(e.target)) {
            logoutBox.style.display = 'none';
        }
    });
</script>
</body>
</html>