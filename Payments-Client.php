<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FreshFold Payments</title>
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
        }

        .user-profile img {
            width: 18px;
            height: 18px;
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

        .sidebar ul li.active {
            background-color: #75b2f0;
        }

        .sidebar ul li.active span {
            color: rgb(255, 255, 255);
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

        .search-container {
            display: flex;
            justify-content: flex-start; 
            align-items: center;         
            gap: 10px;
            margin-top: 20px;            
        }

        .search-container input {
            padding: 8px;
            border: 1px solid #aed1fc;
            border-radius: 5px;
            width: 250px;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .payment-table th,
        .payment-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        .payment-table th {
            background-color: #165a91;
            color: white;
        }

        .status {
            font-weight: bold;
        }

        .status.paid {
            color: green;
        }

        .status.partially {
            color: goldenrod;
        }

        .user-dropdown {
            position: relative;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
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
            <img src="FFLSlogo.png" alt="FreshFold Logo" style="height: 50px;">
            <button id="menu-btn"><img src="m-icon.png" alt="Menu"></button>
        </div>
        <div class="user-dropdown" id="userDropdown">
            <span onclick="toggleLogout()">User</span>
            <img src="ad-icon.png" alt="Dropdown Icon" style="width: 12px; height: 12px;" onclick="toggleLogout()">
            <div class="logout-box" id="logoutBox">
                <a href="login.html">Logout</a>
            </div>
        </div>
        
    </header>

    <div class="sidebar" id="sidebar">
        <ul>
            <li><img src="d-icon.png" alt="Dashboard Icon"><a href="Dashboard-Client.html">Dashboard</a></li>
            <li><img src="O-icon.png" alt="Orders Icon"><a href="Orders-Client.html">Orders</a></li>
            <li class="active"><img src="p-icon.png" alt="Payments Icon"><span>Payments</span></li>
            <li><img src="OT-icon.png" alt="Order Tracking Icon"><a href="Order_Tracking-Client.html">Order Tracking</a></li>
        </ul>
    </div>

    <div class="content" id="main-content">
        <h2>Payments History</h2>

        <div class="search-container">
            <input type="text" placeholder="Search Date or Status">
        </div>

        <table class="payment-table">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Order #</th>
                    <th>Date Paid</th>
                    <th>Amount Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>P-2025-001</td>
                    <td>1023</td>
                    <td>Mar 21, 2025</td>
                    <td>₱250.00</td>
                    <td>₱0.00</td>
                    <td><span class="status partially">Partially Paid</span></td>
                </tr>
                <tr>
                    <td>P-2025-002</td>
                    <td>1024</td>
                    <td>Feb 15, 2025</td>
                    <td>₱100.00</td>
                    <td>₱80.00</td>
                    <td><span class="status paid">Paid</span></td>
                </tr>
                <tr>
                    <td>P-2025-003</td>
                    <td>1025</td>
                    <td>Jan 30, 2025</td>
                    <td>₱350.00</td>
                    <td>₱0.00</td>
                    <td><span class="status paid">Paid</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        const menuBtn = document.getElementById('menu-btn');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('main-content');

        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            content.classList.toggle('shift');
        });

        function toggleLogout() {
        const box = document.getElementById("logoutBox");
        box.style.display = box.style.display === "block" ? "none" : "block";
    }

        
        document.addEventListener("click", function (e) {
        const dropdown = document.getElementById("userDropdown");
        const logoutBox = document.getElementById("logoutBox");
        if (!dropdown.contains(e.target)) {
            logoutBox.style.display = "none";
        }
    });
    </script>

</body>
</html>
