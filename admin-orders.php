<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order_id'])) {
    $orderIdToDelete = intval($_POST['delete_order_id']);
    if ($orderIdToDelete > 0) {
        $deleteSql = "DELETE FROM orders WHERE order_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        if ($deleteStmt) {
            $deleteStmt->bind_param("i", $orderIdToDelete);
            if ($deleteStmt->execute()) {
                echo "success"; 
            } else {
                echo "error: " . $deleteStmt->error;
            }
            $deleteStmt->close();
        } else {
            echo "error: " . $conn->error;
        }
    } else {
        echo "error: Invalid order ID for deletion.";
    }
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_order'])) {
    $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $customerName = isset($_POST['customer']) ? mysqli_real_escape_string($conn, $_POST['customer']) : '';
    $serviceType = isset($_POST['service']) ? mysqli_real_escape_string($conn, $_POST['service']) : '';
    $status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : '';
    $dateReceived = isset($_POST['date']) ? mysqli_real_escape_string($conn, $_POST['date']) : '';
    $dueDate = isset($_POST['due']) ? mysqli_real_escape_string($conn, $_POST['due']) : '';
    $weightKg = isset($_POST['weight']) ? floatval($_POST['weight']) : 0.00;
    $paymentAmount = calculatePayment($serviceType, $weightKg);

    if (empty($customerName) || empty($serviceType) || empty($dateReceived) || empty($dueDate)) {
        echo "Error: All fields are required.";
        exit();
    }

    if ($orderId > 0) {
        $updateSql = "UPDATE orders SET customer_name = ?, service_type = ?, status = ?, date_received = ?, due_date = ?, weight_kg = ?, payment_amount = ? WHERE order_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        if ($updateStmt) {
            $updateStmt->bind_param("ssssssdi", $customerName, $serviceType, $status, $dateReceived, $dueDate, $weightKg, $paymentAmount, $orderId);
            if ($updateStmt->execute()) {
                echo "success"; 
            } else {
                echo "error: " . $updateStmt->error;
            }
            $updateStmt->close();
        } else {
            echo "error: " . $conn->error;
        }
    } else {
        $insertSql = "INSERT INTO orders (customer_name, service_type, status, date_received, due_date, weight_kg, payment_amount) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        if ($insertStmt) {
            $insertStmt->bind_param("ssssssd", $customerName, $serviceType, $status, $dateReceived, $dueDate, $weightKg, $paymentAmount);
            if ($insertStmt->execute()) {
                echo "success"; 
            } else {
                echo "error: " . $insertStmt->error;
            }
            $insertStmt->close();
        } else {
            echo "error: " . $conn->error;
        }
    }
    exit();
}

function calculatePayment($service, $weight) {
    $rate = ($service === "Full Service") ? 22.5 : 6.25;
    return round($rate * $weight, 2);
}

$sql = "SELECT order_id, customer_name, service_type, status, date_received, due_date, weight_kg, payment_amount FROM orders";
$result = $conn->query($sql);
$orders = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshFold Laundry - Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            color: white;
            font-size: 20px;
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
            margin-left: 20px;
            margin-top: 80px;
            padding: 20px;
            transition: margin-left 0.3s ease-in-out;
            overflow-y: auto;
            height: calc(100vh - 80px);
        }

        .content.shift {
            margin-left: 260px;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .search-bar {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .search-bar input {
            padding: 8px 15px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #96c7f9;
            font-weight: bold;
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .action-link {
            text-decoration: none;
            margin-right: 10px;
            font-weight: bold;
        }

        .action-link.view {
          color: blue;
        }

        .action-link.edit {
          color: dodgerblue;
        }

        .action-link.delete {
            color: #f44336;
        }

        .action-link:hover {
            text-decoration: underline;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 600px;
            max-width: 90%;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-primary {
            background: #82b8ef;

        }

        .btn-secondary {
            background: #f1f1f1;
            color: #333;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }

        .status-ready-for-pick-up {
            color: #28a745;
            font-weight: bold;
        }

        .status-completed {
            color: #17a2b8;
            font-weight: bold;
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
            <img src="FFLSlogo.png" alt="Logo" style="height: 40px;">
            <button id="menu-btn">
                <img src="m-icon.png" alt="Menu">
            </button>
        </div>
        <div class="user-profile" id="logout-btn">
            <span>Admin</span>
            <i class="fas fa-user-circle" id="profile-icon"></i>
            <div class="logout-box"id="logout-box">
                <a href="login.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="sidebar" id="sidebar">
        <ul>
            <li><a href="admin-dashboard.php"><img src="d-icon.png"></i> Dashboard</a></li>
            <li class="active-menu"><a href="admin-orders.php"><img src="O-icon.png"><i class="Orders"></i> Orders</a></li>
            <li><a href="admin-customers.php"><img src="c-icon.png"></i> Customers</a></li>
            <li><a href="admin-inventory.php"><img src="i-icon.png"></i> Inventory</a></li>
            <li><a href="admin-Paymentsss.php"><img src="p-icon.png"></i> Payments</a></li>
            <li><a href="admin-reports.php"><img src="rp-icon.png"></i> Reports</a></li>
        </ul>
    </div>

    <div class="content" id="mainContent">
        <h1>Orders</h1>

        <div class="search-bar">
            <input type="text" id="search-input" onkeyup="filterOrders()" placeholder="Search for orders...">
            <button class="btn btn-primary" onclick="openAddOrderModal()">Add New Order</button>
        </div>

        <table id="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Service Type</th>
                    <th>Status</th>
                    <th>Date Received</th>
                    <th>Due Date</th>
                    <th>Weight (kg)</th>
                    <th>Payment (₱)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan='9'>No orders found</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr id="row-<?php echo htmlspecialchars($order['order_id']); ?>" data-order='<?php echo htmlspecialchars(json_encode($order)); ?>'>
                            <td><?php echo htmlspecialchars(sprintf('%04d', $order["order_id"])); ?></td>
                            <td><?php echo htmlspecialchars($order["customer_name"]); ?></td>
                            <td><?php echo htmlspecialchars($order["service_type"]); ?></td>
                            <td><span class='status-<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $order["status"]))); ?>'><?php echo htmlspecialchars($order["status"]); ?></span></td>
                            <td><?php echo htmlspecialchars($order["date_received"]); ?></td>
                            <td><?php echo htmlspecialchars($order["due_date"]); ?></td>
                            <td><?php echo htmlspecialchars(number_format($order["weight_kg"], 2)); ?></td>
                            <td>₱<?php echo htmlspecialchars(number_format($order["payment_amount"], 2)); ?></td>
                            <td>
                                <span class='action-link' onclick='openEditOrderModal(<?php echo htmlspecialchars($order["order_id"]); ?>)'>Edit</span>
                                <span class='action-link delete' onclick='confirmDeleteOrder(<?php echo htmlspecialchars($order["order_id"]); ?>, "<?php echo htmlspecialchars($order["customer_name"]); ?>")'>Delete</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div id="add-edit-order-modal" class="modal">
            <div class="modal-content">
                <h2 id="modal-title">Add New Order</h2>
                <form id="add-edit-order-form" action="admin-orders.php" method="POST">
                    <input type="hidden" name="save_order" value="true">
                    <input type="hidden" id
                    <input type="hidden" id="order-id" name="order_id" value="0">
                    <div class="form-group">
                        <label for="customer">Customer Name</label>
                        <input type="text" id="customer" name="customer" required>
                    </div>
                    <div class="form-group">
                        <label for="service">Service Type</label>
                        <select id="service" name="service" required>
                            <option value="">Select Service</option>
                            <option value="Wash Only">Wash Only</option>
                            <option value="Wash and Fold">Wash and Fold</option>
                            <option value="Full Service">Full Service</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Washing">Washing</option>
                            <option value="Drying">Drying</option>
                            <option value="Folding">Folding</option>
                            <option value="Ready for Pick Up">Ready for Pick Up</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date">Date Received</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="due">Due Date</label>
                        <input type="date" id="due" name="due" required>
                    </div>
                    <div class="form-group">
                        <label for="weight">Weight (kg)</label>
                        <input type="number" id="weight" name="weight" step="0.01" required>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('add-edit-order-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Order</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="delete-order-modal" class="modal">
            <div class="modal-content">
                <h2>Confirm Delete</h2>
                <p>Are you sure you want to delete order for <span id="delete-customer-name"></span>?</p>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('delete-order-modal')">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="deleteOrder()">Delete</button>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            let orderToDeleteId = null;

            document.getElementById('menu-btn').addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('active');
                document.getElementById('mainContent').classList.toggle('shift');
            });

            document.getElementById('logout-btn').addEventListener('click', function() {
                document.getElementById('logout-box').style.display =
                    document.getElementById('logout-box').style.display === 'block' ? 'none' : 'block';
            });

            function openAddOrderModal() {
                document.getElementById('modal-title').textContent = 'Add New Order';
                document.getElementById('order-id').value = 0;
                document.getElementById('customer').value = '';
                document.getElementById('service').value = '';
                document.getElementById('status').value = '';
                document.getElementById('date').value = '';
                document.getElementById('due').value = '';
                document.getElementById('weight').value = '';
                document.getElementById('add-edit-order-modal').style.display = 'flex';
            }

            function openEditOrderModal(id) {
                const row = document.getElementById(`row-${id}`);
                if (row) {
                    const orderData = JSON.parse(row.dataset.order);
                    document.getElementById('modal-title').textContent = 'Edit Order';
                    document.getElementById('order-id').value = orderData.order_id;
                    document.getElementById('customer').value = orderData.customer_name;
                    document.getElementById('service').value = orderData.service_type;
                    document.getElementById('status').value = orderData.status;
                    document.getElementById('date').value = orderData.date_received;
                    document.getElementById('due').value = orderData.due_date;
                    document.getElementById('weight').value = orderData.weight_kg;
                    document.getElementById('add-edit-order-modal').style.display = 'flex';
                } else {
                    alert('Order data not found.');
                }
            }

            document.getElementById('add-edit-order-form').addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(this);

                fetch('admin-orders.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        alert(document.getElementById('order-id').value === '0' ? 'Order added successfully!' : 'Order updated successfully!');
                        closeModal('add-edit-order-modal');
                        location.reload();
                    } else {
                        alert('Error saving order: ' + data);
                    }
                })
                .catch(error => {
                    console.error('Error saving order:', error);
                    alert('An error occurred while saving the order.');
                });
            });

            function confirmDeleteOrder(id, customerName) {
                orderToDeleteId = id;
                document.getElementById('delete-customer-name').textContent = customerName;
                document.getElementById('delete-order-modal').style.display = 'flex';
            }

            function deleteOrder() {
                if (orderToDeleteId !== null) {
                    const formData = new FormData();
                    formData.append('delete_order_id', orderToDeleteId);

                    fetch('admin-orders.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data === 'success') {
                            const rowToDelete = document.getElementById(`row-${orderToDeleteId}`);
                            if (rowToDelete) {
                                rowToDelete.remove();
                            }
                            alert(`Order ${orderToDeleteId} deleted successfully!`);
                            closeModal('delete-order-modal');
                            orderToDeleteId = null;
                        } else {
                            alert('Failed to delete order.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            }

            function closeModal(modalId) {
                document.getElementById(modalId).style.display = 'none';
            }

            function filterOrders() {
                const searchInput = document.getElementById('search-input').value.toLowerCase();
                const rows = document.querySelectorAll('#orders-table tbody tr');

                rows.forEach(row => {
                    const orderID = row.cells[0].textContent.toLowerCase();
                    const customerName = row.cells[1].textContent.toLowerCase();
                    const serviceType = row.cells[2].textContent.toLowerCase();
                    const status = row.cells[3].textContent.toLowerCase();

                    if (orderID.includes(searchInput) || customerName.includes(searchInput) || serviceType.includes(searchInput) || status.includes(searchInput)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            jQuery.expr[':'].contains = function(a, i, m) {
                return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
            };
        </script>
    </div>
</body>
</html>
