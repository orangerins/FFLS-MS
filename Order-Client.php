<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshFold Laundry Services</title>
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

        .orders-controls {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            align-items: center;
        }

        
        #add-order-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background-color: #82b8ef;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        #add-order-btn img {
            width: 18px;
            height: 18px;
        }

        
        #search-bar {
            padding: 8px;
            width: 200px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 14px;
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

        #add-order-form {
            margin-top: 20px;
            background: #f1f1f1;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            margin: 0 auto;
        }

        #add-order-form input {
            margin-bottom: 10px;
            padding: 8px;
            width: 100%;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        #add-order-form button {
            background-color: #82b8ef;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            border-radius: 5px;
        }

        #cancel-order-btn {
            background-color: #f44336;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            border-radius: 5px;
            width: 100%;
        }

        .status-pending {
            color: rgb(201, 201, 18);
            text-align: center;
            font-weight: bold;
        }

        .status-complete {
            color: green;
            text-align: center;
            font-weight: bold;
        }

        .status-ready {
            color: #82b8ef;
            text-align: center;
            font-weight: bold;
        }

        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
        }

        .modal-content input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .modal-content button {
            background-color: #82b8ef;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            border-radius: 5px;
        }

        .close {
            background-color: #f44336;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
            border-radius: 5px;
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
            <li><img src="p-icon.png" alt="Payments Icon"><a href="Payments-Client.html">Payments</a></li>
            <li><img src="OT-icon.png" alt="Order Tracking Icon"><a href="Order_Tracking-Client.html">Order Tracking</a></li>            
        </ul>
    </div>

    <div class="content" id="content">
        <h2>Orders</h2>
        <div class="orders-controls">
            <button id="add-order-btn"><img src="add-icon.png" alt="Add Order"> Add Order</button>
            <input type="text" id="search-bar" placeholder="Search Orders..." />
        </div>

        <table id="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date Placed</th>
                    <th>Weight (kg)</th>
                    <th>Total Price (PHP)</th>
                    <th>Contact #</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="orders-tbody">
                
            </tbody>
        </table>

        <p id="no-orders-message" style="display: none;">No orders yet. Please add some orders.</p>

        <div id="add-order-form" style="display: none;">
            <h3>Add New Order</h3>
            <form id="order-form">
                <label for="date-placed">Date Placed:</label>
                <input type="date" id="date-placed" required><br>

                <label for="weight">Weight (kg):</label>
                <input type="number" id="weight" required min="1"><br>

                <label for="contact-number">Contact Number:</label>
                <input type="tel" id="contact-number" required><br>

                <button type="submit">Add Order</button>
            </form>
            <button id="cancel-order-btn">Cancel</button>
        </div>
    </div>

    
    <div id="view-order-modal" class="modal">
        <div class="modal-content">
            <h3>Order Details</h3>
            <p><strong>Order ID:</strong> <span id="view-order-id"></span></p>
            <p><strong>Date Placed:</strong> <span id="view-date-placed"></span></p>
            <p><strong>Weight:</strong> <span id="view-weight"></span> kg</p>
            <p><strong>Total Price:</strong> ₱<span id="view-total-price"></span></p>
            <p><strong>Contact #:</strong> <span id="view-contact-number"></span></p>
            <p><strong>Status:</strong> <span id="view-status"></span></p>
            <button class="close" onclick="closeModal('view-order-modal')">Close</button>
        </div>
    </div>

   
    <div id="edit-order-modal" class="modal">
        <div class="modal-content">
            <h3>Edit Order</h3>
            <form id="edit-order-form">
                <input type="hidden" id="edit-order-id">
                <label for="edit-date-placed">Date Placed:</label>
                <input type="date" id="edit-date-placed" required><br>

                <label for="edit-weight">Weight (kg):</label>
                <input type="number" id="edit-weight" required min="1"><br>

                <label for="edit-contact-number">Contact Number:</label>
                <input type="tel" id="edit-contact-number" required><br>

                <button type="submit">Update Order</button>
            </form>
            <button class="close" onclick="closeModal('edit-order-modal')">Close</button>
        </div>
    </div>

    <script>
        let orderId = 1;
        let orders = [];

        document.getElementById('menu-btn').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            sidebar.classList.toggle('active');
            content.classList.toggle('shift');
        });
        
        document.getElementById('add-order-btn').addEventListener('click', function() {
            document.getElementById('add-order-form').style.display = 'block';
            document.getElementById('orders-table').style.display = 'none';
        });

        
        document.getElementById('cancel-order-btn').addEventListener('click', function() {
            document.getElementById('add-order-form').style.display = 'none';
            document.getElementById('orders-table').style.display = 'table';
        });

      
        document.getElementById('order-form').addEventListener('submit', function(event) {
    event.preventDefault();  
    console.log("Form submitted"); 

    const datePlaced = document.getElementById('date-placed').value;
    const weight = parseFloat(document.getElementById('weight').value);
    const contactNumber = document.getElementById('contact-number').value;

    console.log("Date Placed:", datePlaced);  
    console.log("Weight:", weight);
    console.log("Contact Number:", contactNumber);

    
    if (isNaN(weight) || weight <= 0) {
        console.error("Invalid weight entered");
        return;
    }

   
    if (!contactNumber) {
        console.error("Contact number is required");
        return;
    }

    const totalPrice = (weight / 8) * 180; 
    console.log("Total Price:", totalPrice);  

   
    const newOrder = {
        id: orderId++,  
        datePlaced,
        weight,
        totalPrice,
        contactNumber,
        status: 'Pending'
    };

    console.log("New Order:", newOrder); 

    orders.push(newOrder);
    console.log("Orders Array:", orders);

    renderOrders();

    document.getElementById('order-form').reset();
    document.getElementById('add-order-form').style.display = 'none';
    document.getElementById('orders-table').style.display = 'table';
});


        function renderOrders() {
            const ordersTbody = document.getElementById('orders-tbody');
            ordersTbody.innerHTML = '';
            orders.forEach(order => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${order.id}</td>
                    <td>${order.datePlaced}</td>
                    <td>${order.weight}</td>
                    <td>₱${order.totalPrice}</td>
                    <td>${order.contactNumber}</td>
                    <td><span class="status-pending">${order.status}</span></td>
                    <td>
                        <button onclick="viewOrder(${order.id})">View</button>
                        <button onclick="editOrder(${order.id})">Edit</button>
                    </td>
                `;
                ordersTbody.appendChild(row);
            });

            if (orders.length === 0) {
                document.getElementById('no-orders-message').style.display = 'block';
            } else {
                document.getElementById('no-orders-message').style.display = 'none';
            }
        }

       
        function viewOrder(orderId) {
            const order = orders.find(o => o.id === orderId);
            document.getElementById('view-order-id').textContent = order.id;
            document.getElementById('view-date-placed').textContent = order.datePlaced;
            document.getElementById('view-weight').textContent = order.weight;
            document.getElementById('view-total-price').textContent = order.totalPrice;
            document.getElementById('view-contact-number').textContent = order.contactNumber;
            document.getElementById('view-status').textContent = order.status;
            document.getElementById('view-order-modal').style.display = 'flex';
        }

      
        function editOrder(orderId) {
            const order = orders.find(o => o.id === orderId);
            document.getElementById('edit-order-id').value = order.id;
            document.getElementById('edit-date-placed').value = order.datePlaced;
            document.getElementById('edit-weight').value = order.weight;
            document.getElementById('edit-contact-number').value = order.contactNumber;
            document.getElementById('edit-order-modal').style.display = 'flex';
        }

        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

       
        document.getElementById('edit-order-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const orderId = parseInt(document.getElementById('edit-order-id').value);
            const datePlaced = document.getElementById('edit-date-placed').value;
            const weight = parseFloat(document.getElementById('edit-weight').value);
            const contactNumber = document.getElementById('edit-contact-number').value;

            const totalPrice = (weight / 8) * 180;  

            const orderIndex = orders.findIndex(o => o.id === orderId);
            orders[orderIndex] = {
                ...orders[orderIndex],
                datePlaced,
                weight,
                totalPrice,
                contactNumber
            };

            renderOrders();
            closeModal('edit-order-modal');
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
