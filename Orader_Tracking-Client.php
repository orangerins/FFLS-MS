<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FreshFold Order Tracking</title>
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
        }

        .search-container input {
            padding: 8px;
            border: 1px solid #aed1fc;
            border-radius: 5px;
            width: 250px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #165a91;
            color: white;
        }

        .status {
            font-weight: bold;
        }

        .status.washing { color: goldenrod; }
        .status.completed { color: green; }
        .status.pickup { color: #4dc3ff; }

        .view-link {
            color: #165a91;
            cursor: pointer;
            text-decoration: underline;
        }

        .details {
            display: none;
            background: #d9ecff;
            padding: 15px;
            border-radius: 8px;
            width: 60%;
            margin-top: 20px;
        }

        .details h3 {
            margin-bottom: 10px;
        }

        .progress span {
            display: block;
            margin: 4px 0;
        }

        .done { color: green; }
        .in-progress { color: orange; }
        .not-started { color: red; }

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
        <li><img src="p-icon.png" alt="Payments Icon"><a href="Payments-Client.html">Payments</a></li>
        <li class="active"><img src="OT-icon.png" alt="Order Tracking Icon"><span>Order Tracking</span></li>
    </ul>
</div>

<div class="content" id="main-content">
    <h2>Track Order</h2>

    <div class="search-container">
        <input type="text" placeholder="Search Date Placed or Status...">
    </div>

    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Date Placed</th>
                <th>Items</th>
                <th>Total Price</th>
                <th>Current Status</th>
                <th>Estimated Completion</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1023</td>
                <td>Mar 21, 2025</td>
                <td>5 kg</td>
                <td>₱250.00</td>
                <td class="status washing">Washing</td>
                <td>Mar 22, 2025</td>
                <td><span class="view-link" onclick="showDetails(1)">View</span></td>
            </tr>
            <tr>
                <td>1024</td>
                <td>Feb 15, 2025</td>
                <td>3 kg</td>
                <td>₱180.00</td>
                <td class="status completed">Completed</td>
                <td>Feb 15, 2025</td>
                <td><span class="view-link" onclick="showDetails(2)">View</span></td>
            </tr>
            <tr>
                <td>1025</td>
                <td>Jan 30, 2025</td>
                <td>7 kg</td>
                <td>₱350.00</td>
                <td class="status pickup">Ready for Pickup</td>
                <td>Jan 30, 2025</td>
                <td><span class="view-link" onclick="showDetails(3)">View</span></td>
            </tr>
        </tbody>
    </table>

  
    <div class="details" id="detail1">
        <h3>Order Details</h3>
        <p><strong>Order ID:</strong> 1023</p>
        <p><strong>Date Placed:</strong> Mar 21, 2025</p>
        <p><strong>Items:</strong> 5 kg Regular Wash</p>
        <p><strong>Total Price:</strong> ₱250.00</p>
        <p><strong>Payment Status:</strong> Partially Paid</p>
        <h4>Order Progress Tracker:</h4>
        <div class="progress">
            <span class="done">Order Received: Done</span>
            <span class="in-progress">Washing: In Progress</span>
            <span class="not-started">Drying: Not Started</span>
            <span class="not-started">Folding: Not Started</span>
            <span class="not-started">Ready for Pickup: Not Started</span>
        </div>
    </div>

    <div class="details" id="detail2">
        <h3>Order Details</h3>
        <p><strong>Order ID:</strong> 1024</p>
        <p><strong>Date Placed:</strong> Feb 15, 2025</p>
        <p><strong>Items:</strong> 3 kg Regular Wash</p>
        <p><strong>Total Price:</strong> ₱180.00</p>
        <p><strong>Payment Status:</strong> Paid</p>
        <h4>Order Progress Tracker:</h4>
        <div class="progress">
            <span class="done">Order Received: Done</span>
            <span class="done">Washing: Done</span>
            <span class="done">Drying: Done</span>
            <span class="done">Folding: Done</span>
            <span class="done">Ready for Pickup: Done</span>
        </div>
    </div>

    <div class="details" id="detail3">
        <h3>Order Details</h3>
        <p><strong>Order ID:</strong> 1025</p>
        <p><strong>Date Placed:</strong> Jan 30, 2025</p>
        <p><strong>Items:</strong> 7 kg Regular Wash</p>
        <p><strong>Total Price:</strong> ₱350.00</p>
        <p><strong>Payment Status:</strong> Paid</p>
        <h4>Order Progress Tracker:</h4>
        <div class="progress">
            <span class="done">Order Received: Done</span>
            <span class="done">Washing: Done</span>
            <span class="done">Drying: Done</span>
            <span class="done">Folding: Done</span>
            <span class="in-progress">Ready for Pickup: In Progress</span>
        </div>
    </div>
</div>

<script>
    const menuBtn = document.getElementById('menu-btn');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('main-content');

    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        content.classList.toggle('shift');
    });

    function showDetails(id) {
        document.querySelectorAll('.details').forEach(d => d.style.display = 'none');
        const detailBox = document.getElementById('detail' + id);
        if (detailBox) {
            detailBox.style.display = 'block';
            detailBox.scrollIntoView({ behavior: 'smooth' });
        }
    }

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
