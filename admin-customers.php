<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Customers | FreshFold Laundry Services</title>
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

        h2 {
            margin-top: 20px;
            color: #333;
        }

        .actions {
            display: flex;
            gap: 5px;
        }

        .action-btn {
            padding: 4px 8px;
            font-size: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .view { background-color: #d3eaff; }
        .edit { background-color: #e1f5d3; }
        .delete { background-color: #ffcccc; }

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
            color: white;
        }

        .controls {
            display: flex;
            gap: 10px;
            margin: 20px 0 10px 0;
        }

        .controls input, .controls button {
            padding: 6px 10px;
            border: none;
            border-radius: 15px;
            background: #f1f5ff;
            font-size: 14px;
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
        <div class="user-profile" id="logout-btn">
            <span>Admin</span>
            <img src="ad-icon.png" alt="User Icon" id="profile-icon">
            <div class="logout-box" id="logout-box">
                <a href="login.html">Logout</a>
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

    <div class="content" id="main-content">
        <h2>Customer List</h2>
        <div class="controls">
            <input type="text" placeholder="Search by name or email" id="searchInput">
            <button onclick="addCustomer()">Add New Customer</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone #</th>
                    <th>Address</th>
                    <th>Total Orders</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="customerTableBody">
                <tr>
                    <td>001</td>
                    <td>Kim Chiu</td>
                    <td>kimc@gmail.com</td>
                    <td>09090909</td>
                    <td>Tambacan</td>
                    <td>4</td>
                    <td class="actions">
                        <button class="action-btn view" onclick="viewCustomer(this)">View</button>
                        <button class="action-btn edit" onclick="editCustomer(this)">Edit</button>
                        <button class="action-btn delete" onclick="deleteCustomer(this)">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        const menuBtn = document.getElementById('menu-btn');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('main-content');
        const profileIcon = document.getElementById('profile-icon');
        const logoutBox = document.getElementById('logout-box');
        const searchInput = document.getElementById('searchInput');

        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            content.classList.toggle('shift');
        });

        profileIcon.addEventListener('click', () => {
            logoutBox.style.display = logoutBox.style.display === 'block' ? 'none' : 'block';
        });

        window.addEventListener('click', (e) => {
            if (!e.target.closest('.user-profile')) {
                logoutBox.style.display = 'none';
            }
        });

        function addCustomer() {
            const table = document.getElementById("customerTableBody");
            const newRow = table.insertRow();
            newRow.innerHTML = `
                <td>002</td>
                <td>New Customer</td>
                <td>new@example.com</td>
                <td>0912121212</td>
                <td>New Address</td>
                <td>0</td>
                <td class="actions">
                    <button class="action-btn view" onclick="viewCustomer(this)">View</button>
                    <button class="action-btn edit" onclick="editCustomer(this)">Edit</button>
                    <button class="action-btn delete" onclick="deleteCustomer(this)">Delete</button>
                </td>
            `;
        }

        function viewCustomer(btn) {
            alert("Viewing customer: " + btn.closest("tr").children[1].textContent);
        }

        function editCustomer(btn) {
            alert("Editing customer: " + btn.closest("tr").children[1].textContent);
        }

        function deleteCustomer(btn) {
            const row = btn.closest("tr");
            if (confirm("Are you sure you want to delete this customer?")) {
                row.remove();
            }
        }

        searchInput.addEventListener('input', () => {
            const filter = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('#customerTableBody tr');
            rows.forEach(row => {
                const name = row.children[1].textContent.toLowerCase();
                const email = row.children[2].textContent.toLowerCase();
                if (name.includes(filter) || email.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
