<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FreshFold Laundry - Reports</title>
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

        .date-range-selector {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .date-range-selector select,
        .date-range-selector input {
            padding: 5px;
            font-size: 14px;
        }

        .btn-apply {
            padding: 6px 15px;
            background: #82b8ef;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-apply:hover {
            background: #68a6d5;
        }

        .status-pending {
            color: orange;
        }

        .status-completed {
            color: green;
        }

        .status-cancelled {
            color: red;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo-menu">
            <img src="FFLSlogo.png" alt="FreshFold Logo" style="height: 50px;">
            <button id="menu-btn"><img src="m-icon.png" alt="Menu"></button>
        </div>
        <div class="user-profile" id="logout-btn">
            <div class="user-dropdown" id="userDropdown">
                <span onclick="toggleLogout()">Admin</span>
                <img src="ad-icon.png" alt="Dropdown Icon" style="width: 12px; height: 12px;" onclick="toggleLogout()">
                <div class="logout-box" id="logoutBox">
                    <a href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="sidebar" id="sidebar">
        <ul>
            <li><img src="d-icon.png" alt="Dashboard Icon"><a href="admin-dashboard.html">Dashboard</a></li>
            <li><img src="O-icon.png" alt="Orders Icon"><a href="admin-orders.html">Orders</a></li>
            <li><img src="c-icon.png" alt="Customers Icon"><a href="admin-customers.html">Customers</a></li>
            <li><img src="i-icon.png" alt="Inventory Icon"><a href="admin-inventory.html">Inventory</a></li>
            <li><img src="p-icon.png" alt="Payments Icon"><a href="admin-payments.html">Payments</a></li>
            <li><img src="rp-icon.png" alt="Reports Icon"><a href="admin-reports.html">Reports</a></li>
        </ul>
    </div>

    <div class="content" id="mainContent">
        <div class="dashboard-welcome">
            <h1>Reports</h1>
        </div>

        <div class="date-range-selector">
            <select>
                <option>Today</option>
                <option>Yesterday</option>
                <option>This Week</option>
                <option selected>This Month</option>
                <option>Last Month</option>
                <option>Custom Range</option>
            </select>
            <input type="date" id="startDate" value="2025-03-01" />
            <span>to</span>
            <input type="date" id="endDate" value="2025-03-31" />
            <button class="btn btn-apply">Apply</button>
        </div>

        <div class="statistics">
            <div class="stat-card"><h3>Total Sales</h3><p>₱12,450</p></div>
            <div class="stat-card"><h3>Total Orders</h3><p>50</p></div>
            <div class="stat-card"><h3>Total Customers</h3><p>10</p></div>
            <div class="stat-card"><h3>Revenue</h3><p>₱9,870</p></div>
        </div>

        <div class="orders-summary">
            <h2>Order Summary</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order #</th>
                        <th>Customer Name</th>
                        <th>Amount Paid</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="orderTableBody"></tbody>
            </table>
        </div>
    </div>

    <script>
        const orders = [
            {
                orderId: '001',
                customerName: 'Jay Xyz',
                dateReceived: '2025-03-20',
                status: 'Pending',
                dueDate: '2025-03-30',
                totalAmount: '₱500',
                method: 'Cash',
            },
            {
                orderId: '002',
                customerName: 'Jamie Tan',
                dateReceived: '2025-03-19',
                status: 'Completed',
                dueDate: '2025-03-29',
                totalAmount: '₱300',
                method: 'Cash',
            },
            {
                orderId: '003',
                customerName: 'Kween Lengleng',
                dateReceived: '2025-03-18',
                status: 'Completed',
                dueDate: '2025-03-28',
                totalAmount: '₱450',
                method: 'Cash',
            },
            {
                orderId: '004',
                customerName: 'Sassa Gurl',
                dateReceived: '2025-03-17',
                status: 'Cancelled',
                dueDate: 'N/A',
                totalAmount: '₱275',
                method: 'Cash',
            }
        ];

        function renderOrders(data) {
            const tbody = document.getElementById("orderTableBody");
            tbody.innerHTML = "";

            data.forEach((order) => {
                const row = `
                    <tr>
                        <td>${new Date(order.dateReceived).toLocaleDateString()}</td>
                        <td>${order.orderId}</td>
                        <td>${order.customerName}</td>
                        <td>${order.totalAmount}</td>
                        <td>${order.method}</td>
                        <td class="status-${order.status.toLowerCase()}">${order.status}</td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        function applyFilter() {
            const start = new Date(document.getElementById("startDate").value);
            const end = new Date(document.getElementById("endDate").value);

            const filtered = orders.filter(order => {
                const date = new Date(order.dateReceived);
                return date >= start && date <= end;
            });

            renderOrders(filtered);
        }

        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const content = document.getElementById("mainContent");
            sidebar.classList.toggle("active");
            content.classList.toggle("shift");
        }

        document.querySelector('#menu-btn').addEventListener('click', toggleSidebar);
        document.querySelector('.btn-apply').addEventListener('click', applyFilter);

        renderOrders(orders);

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
