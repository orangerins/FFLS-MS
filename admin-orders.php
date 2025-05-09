<?php
include 'db_connection.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getInventoryUsageForService($service, $weight) {
    $usage = [];
    if ($service === "Washing" || $service === "Folding+Washing+Drying") {
        $usage[] = ['item_name' => 'Detergent', 'quantity' => ceil($weight / 5)];
    }
    if ($service === "Drying" || $service === "Folding+Washing+Drying") {
        $usage[] = ['item_name' => 'Packaging', 'quantity' => 1];
    }
    return $usage;
}
function sendTextResponse($text) {
    header('Content-Type: text/plain');
    echo $text;
    exit;
}

function handleAddOrder($con) {
    $required = ['customer', 'service', 'weight', 'status', 'date', 'due'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            sendTextResponse("Error: Missing required field: $field");
        }
    }

    $customer = trim($_POST['customer']);
    $service = trim($_POST['service']);
    $weight = floatval($_POST['weight']);
    $status = trim($_POST['status']);
    $date = trim($_POST['date']);
    $due = trim($_POST['due']);
    $payment = calculatePayment($service, $weight);

    $nameParts = explode(' ', $customer);
    $firstName = $nameParts[0] ?? '';
    $lastName = $nameParts[1] ?? '';

    $stmt = $con->prepare("SELECT customer_id FROM customer WHERE first_name = ? OR last_name = ? OR CONCAT(first_name, ' ', last_name) = ?");
    $stmt->bind_param("sss", $firstName, $lastName, $customer);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $customer_id = $row['customer_id'];
        $stmt->close();

        $stmt = $con->prepare("INSERT INTO orders (customer_id, service_type, weight, status, date_placed, due_date, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isddssd", $customer_id, $service, $weight, $status, $date, $due, $payment);

        if ($stmt->execute()) {
            sendTextResponse("success|{$con->insert_id}");
        } else {
            sendTextResponse("Error: Database error: " . $stmt->error);
        }
        $stmt->close();
    } else {
        sendTextResponse("Error: Customer not found: $customer");
    }
}

function handleDeleteOrder($con) {
    if (isset($_POST['order_id'])) {
        $orderIdToDelete = intval($_POST['order_id']);
        $stmt = $con->prepare("DELETE FROM orders WHERE order_id = ?");
        $stmt->bind_param("i", $orderIdToDelete);
        if ($stmt->execute()) {
            sendTextResponse("success|Order {$orderIdToDelete} deleted successfully");
        } else {
            sendTextResponse("Error: Database error deleting order: " . $stmt->error);
        }
        $stmt->close();
    } else {
        sendTextResponse("Error: Order ID not specified for deletion");
    }
}

function handleUpdateOrderStatus($con) {
    if (isset($_POST['order_id']) && isset($_POST['status'])) {
        $orderIdToUpdate = intval($_POST['order_id']);
        $newStatus = trim($_POST['status']);
        $stmt = $con->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $newStatus, $orderIdToUpdate);
        if ($stmt->execute()) {
            sendTextResponse("success|Order {$orderIdToUpdate} status updated to " . htmlspecialchars($newStatus));
        } else {
            sendTextResponse("Error: Database error updating order status: " . $stmt->error);
        }
        $stmt->close();
    } else {
        sendTextResponse("Error: Order ID or status not specified for update");
    }
}

function calculatePayment($service, $weight) {
    if ($service === "Folding+Washing+Drying") {
        return (6.25 + 22.5 + 10.0) * $weight;
    } elseif ($service === "Washing") {
        return 22.5 * $weight;
    } elseif ($service === "Drying") {
        return 10.0 * $weight;
    } elseif ($service === "Folding") {
        return 6.25 * $weight;
    }
    return 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_order':
                handleAddOrder($con);
                break;
            case 'delete_order':
                handleDeleteOrder($con);
                break;
            case 'update_status':
                handleUpdateOrderStatus($con);
                break;
            default:
                sendTextResponse("Error: Invalid action");
                break;
        }
    } else {
        sendTextResponse("Error: No action specified");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

        .sidebar {
            background: #96c7f9;
            width: 240px;
            position: fixed;
            top: 60px;
            left: -240px;
            height: 100%;
            transition: left 0.3s ease;
            padding-top: 10px;
            z-index: 999;
        }
        .sidebar.active { left: 0; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li {
            padding: 12px;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar ul li a img {
            width: 24px;
            height: 24px;
            object-fit: contain;
            vertical-align: middle;
            margin-right: 8px;
        }

        .content {
            margin-left: 20px;
            padding: 20px;
            margin-top: 80px;
            transition: margin-left 0.3s ease;
        }
        .content.shift {
            margin-left: 260px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #96c7f9;
            color: white;
        }
        .status {
            font-weight: bold;
        }
        .status.completed { color: #28a745; }
        .status.pickup { color: #00bfff; }
        .status.pending { color: #ffc107; }
        .btn-group button,
        .btn-view, .btn-edit, .btn-delete {
            margin-right: 5px;
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .btn-view { background: #28a745; }
        .btn-edit { background: #007bff; }
        .btn-delete { background: #dc3545; }
        .btn-view:hover { background: #218838; }
        .btn-edit:hover { background: #0056b3; }
        .btn-delete:hover { background: #c82333; }
        .search-bar {
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .search-bar input {
            padding: 8px;
            width: 300px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .user-profile {
            display: flex;
            align-items: center;
            gap: 5px;
            position: relative;
        }
        .logout-box {
            display: none;
            position: absolute;
            right: 0;
            top: 40px;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 1001;
        }
        .logout-box a {
            color: #165a91;
            text-decoration: none;
            font-size: 14px;
        }
        button.add-btn {
            background: #3498db;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            margin: 10px 0;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
        }
        .modal-content input, .modal-content select {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .modal-content h3 {
            margin-top: 0;
        }
        .modal-content p {
            margin: 8px 0;
        }
        .sub-status {
            margin-left: 15px;
            font-size: 14px;
            color: #555;
        }
        .modal-content button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-save { background-color: #28a745; color: white; }
        .btn-cancel { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <header>
        <div class="logo-menu">
            <img src="FFLSlogo.png" alt="Logo" style="height: 40px;">
            <button id="menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div class="user-profile" id="logout-btn">
            <span>Admin</span>
            <i class="fas fa-user-circle" id="profile-icon"></i>
            <div class="logout-box" id="logout-box">
                <a href="login.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="sidebar" id="sidebar">
        <ul>
            <li><a href="admin-dashboard.php"><img src="d-icon.png"> Dashboard</a></li>
            <li class="active-menu"><a href="admin-orders.php"><img src="O-icon.png"> Orders</a></li>
            <li><a href="admin-customer.php"><img src="c-icon.png"> Customer</a></li>
            <li><a href="admin-inventory.php"><img src="i-icon.png"> Inventory</a></li>
            <li><a href="admin-Paymentsss.php"><img src="p-icon.png"> Payments</a></li>
            <li><a href="admin-reports.php"><img src="rp-icon.png"> Reports</a></li>
        </ul>
    </div>

    <div class="content" id="main-content">
        <h2>Orders</h2>
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="order-search" placeholder="Search by order # or customer name">
        </div>
        <button class="add-btn" onclick="openAddModal()">Add New Order</button>
        <table id="orders-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Status</th>
                <th>Date Received</th>
                <th>Due Date</th>
                <th>Total Price</th>
                <th>Actions</th>
            </tr>
        </thead>
            <tbody id="orders-body">
<?php
$sql = "SELECT o.order_id, CONCAT(c.first_name, ' ', c.last_name) AS customer, o.service_type, o.status, o.date_placed, o.due_date, o.total_price
        FROM orders o
        JOIN customer c ON o.customer_id = c.customer_id
        ORDER BY o.order_id DESC";
$result = $con->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr data-order-id='{$row['order_id']}'>";
        echo "<td>{$row['order_id']}</td>";
        echo "<td>" . htmlspecialchars($row['customer']) . "</td>";
        echo "<td>" . htmlspecialchars($row['service_type']) . "</td>";
        $statusMap = [
            'pending' => 'Pending',
            'washing' => 'Washing',
            'drying' => 'Drying',
            'folding' => 'Folding',
            'ready-to-pickup' => 'Ready for Pickup',
            'completed' => 'Completed'
        ];
        $statusText = $statusMap[$row['status']] ?? ucfirst($row['status']);
        echo "<td><span class='status " . htmlspecialchars($row['status']) . "'>" . htmlspecialchars($statusText) . "</span></td>";
        echo "<td>" . htmlspecialchars($row['date_placed']) . "</td>";
        echo "<td>" . htmlspecialchars($row['due_date']) . "</td>";
        echo "<td>₱" . number_format($row['total_price'], 2) . "</td>";
        echo "<td>
                    <button class='btn-view' data-order-id='{$row['order_id']}'>View</button>
                    <button class='btn-edit' data-order-id='{$row['order_id']}'>Edit</button>
                    <button class='btn-delete' data-order-id='{$row['order_id']}'>Delete</button>
                </td>";
        echo "</tr>";
    }
}
?>
</tbody>
        </table>
    </div>
    <div id="orderModal" class="modal">
      <div class="modal-content" style="width: 400px;">
        <h3 id="orderModalTitle">Order Details</h3>
        <form id="orderModalForm">
          <label>Customer Name</label>
          <input type="text" id="modalCustomer" readonly>
          <label>Service</label>
          <input type="text" id="modalService" readonly>
          <label>Status</label>
          <select id="modalStatus" onchange="updateOrderStatus(this)">
            <option value="pending">Pending</option>
            <option value="washing">Washing</option>
            <option value="drying">Drying</option>
            <option value="folding">Folding</option>
            <option value="ready-to-pickup">Ready for Pickup</option>
            <option value="completed">Completed</option>
          </select>
          <label>Date Received</label>
          <input type="date" id="modalDateReceived" readonly>
          <label>Due Date</label>
          <input type="date" id="modalDueDate" readonly>
          <label>Total Price (₱)</label>
          <input type="text" id="modalPrice" readonly>
          <input type="hidden" id="modalOrderId">
          <div style="margin-top: 16px; text-align: right;">
            <button type="button" class="btn-cancel" onclick="closeOrderModal()">Close</button>
            </div>
        </form>
      </div>
    </div>
    <div id="addOrderModal" class="modal">
    <div class="modal-content">
        <h3>Add New Order</h3>
        <input type="text" id="customerName" placeholder="Customer Name">
        <select id="serviceSelect" onchange="updatePrice()">
            <option value="">Select Service</option>
            <option value="Washing">Washing</option>
            <option value="Drying">Drying</option>
            <option value="Folding+Washing+Drying">Folding+Washing+Drying</option>
        </select>
        <input type="number" id="weightInput" placeholder="Weight (kg)" min="0" step="0.1" oninput="updatePrice()">
        <select id="statusSelect">
            <option value="">Select Status</option>
            <option value="pending">Pending</option>
            <option value="washing">Washing</option>
            <option value="drying">Drying</option>
            <option value="folding">Folding</option>
            <option value="ready-to-pickup">Ready for Pickup</option>
            <option value="completed">Completed</option>
        </select>
        <input type="date" id="dateReceived">
        <input type="date" id="dueDate">
        <div style="margin: 10px 0;">
            <strong>Total Price: ₱<span id="priceDisplay">0.00</span></strong>
        </div>
        <button class="btn-save" onclick="saveOrder()">Save</button>
        <button class="btn-cancel" onclick="closeAddModal()">Cancel</button>
    </div>
</div>

<script>
let currentEditingRow = null;

function openOrderModal(order, isEdit = false, orderId = null) {
    document.getElementById('orderModal').style.display = 'block';
    document.getElementById('modalCustomer').value = order.customer;
    document.getElementById('modalService').value = order.service;
    document.getElementById('modalStatus').value = order.status;
    document.getElementById('modalDateReceived').value = order.date;
    document.getElementById('modalDueDate').value = order.due;
    document.getElementById('modalPrice').value = order.price;
    document.getElementById('modalOrderId').value = orderId;

    document.getElementById('modalCustomer').readOnly = !isEdit;
    document.getElementById('modalService').readOnly = !isEdit;
    document.getElementById('modalStatus').disabled = !isEdit;
    document.getElementById('modalDateReceived').readOnly = !isEdit;
    document.getElementById('modalDueDate').readOnly = !isEdit;
    document.getElementById('modalPrice').readOnly = true; 

    document.getElementById('orderModalTitle').textContent = isEdit ? 'Edit Order' : 'Order Details';
}

function closeOrderModal() {
    document.getElementById('orderModal').style.display = 'none';
    currentEditingRow = null;
}

function updateOrderStatus(selectElement) {
    const orderId = document.getElementById('modalOrderId').value;
    const newStatus = selectElement.value;

    fetch('admin-orders.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'update_status',
            order_id: orderId,
            status: newStatus
        })
    })
    .then(response => response.text())
    .then(text => {
        if (text.startsWith("success|")) {
            const rowToUpdate = document.querySelector(`tr[data-order-id="${orderId}"]`);
            if (rowToUpdate) {
                const statusSpan = rowToUpdate.querySelector('td:nth-child(4) span');
                const statusText = newStatus.replace(/-/g, ' ');
                statusSpan.textContent = capitalize(statusText);
                statusSpan.className = `status ${newStatus}`;
            }
            alert(text.split("|")[1]);
            closeOrderModal();
        } else {
            alert(text.replace("Error: ", ""));
            const originalStatus = currentEditingRow ? currentEditingRow.querySelector('td:nth-child(4) span').classList[1] : '';
            selectElement.value = originalStatus;
        }
    })
    .catch(err => {
        alert('Failed to update order status: ' + err);
        const originalStatus = currentEditingRow ? currentEditingRow.querySelector('td:nth-child(4) span').classList[1] : '';
        selectElement.value = originalStatus;
    });
}

function attachActionListeners(row) {
    row.querySelector('.btn-view').onclick = function() {
        const orderId = this.getAttribute('data-order-id');
        const order = {
            customer: row.children[1].textContent,
            service: row.children[2].textContent,
            status: row.children[3].querySelector('span').classList[1],
            date: row.children[4].textContent,
            due: row.children[5].textContent,
            price: row.children[6].textContent.replace('₱','')
        };
        openOrderModal(order, false, orderId);
    };
    row.querySelector('.btn-edit').onclick = function() {
        const orderId = this.getAttribute('data-order-id');
        const order = {
            customer: row.children[1].textContent,
            service: row.children[2].textContent,
            status: row.children[3].querySelector('span').classList[1],
            date: row.children[4].textContent,
            due: row.children[5].textContent,
            price: row.children[6].textContent.replace('₱','')
        };
        currentEditingRow = row;
        openOrderModal(order, true, orderId);
    };
    row.querySelector('.btn-delete').onclick = function() {
        const orderId = this.getAttribute('data-order-id');
        if (confirm('Are you sure you want to delete order #' + orderId + '?')) {
            deleteOrder(orderId, row);
        }
    };
}

function deleteOrder(orderId, rowElement) {
    fetch('admin-orders.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'delete_order',
            order_id: orderId
        })
    })
    .then(response => response.text())
    .then(text => {
        if (text.startsWith("success|")) {
            rowElement.remove();
            alert(text.split("|")[1]);
        } else {
            alert(text.replace("Error: ", ""));
        }
    })
    .catch(err => {
        alert('Failed to delete order: ' + err);
    });
}

document.querySelectorAll('#orders-body tr').forEach(attachActionListeners);

function updatePrice() {
    const service = document.getElementById('serviceSelect').value;
    const weight = parseFloat(document.getElementById('weightInput').value) || 0;
    let price = 0;
    if (service === "Folding+Washing+Drying") price = (6.25 + 22.5 + 10.0) * weight;
    else if (service === "Washing") price = 22.5 * weight;
    else if (service === "Drying") price = 10.0 * weight;
    else if (service === "Folding") price = 6.25 * weight;
    document.getElementById('priceDisplay').textContent = price.toFixed(2);
}
function saveOrder() {
    const customer = document.getElementById('customerName').value;
    const service = document.getElementById('serviceSelect').value;
    const weight = document.getElementById('weightInput').value;
    const status = document.getElementById('statusSelect').value;
    const date = document.getElementById('dateReceived').value;
    const due = document.getElementById('dueDate').value;
    const price = document.getElementById('priceDisplay').textContent;

    if (!customer || !service || !weight || !status || !date || !due) {
        alert('Please fill in all fields.');
        return;
    }

    fetch('admin-orders.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'add_order',
            customer: customer,
            service: service,
            weight: weight,
            status: status,
            date: date,
            due: due
        })
    })
    .then(response => response.text())
    .then(text => {
        if (text.startsWith("success|")) {
            const order_id = text.split("|")[1];
            addOrderToTable({
                order_id: order_id,
                customer: customer,
                service: service,
                status: status,
                date: date,
                due: due,
                price: price
            });
            closeAddModal();

            fetch('admin-inventory.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    order_inventory_update: 1,
                    service: service,
                    weight: weight
                })
            })
            .then(response => response.text())
            .then(data => {
                if (data !== 'success') {
                    alert('Inventory update failed: ' + data);
                }
            });

        } else {
            alert(text.replace("Error: ", ""));
        }
    })
    .catch(err => {
        alert('Failed to save order: ' + err);
    });
}

document.getElementById('menu-btn').onclick = function() {
    document.getElementById('sidebar').classList.toggle('active');
    document.getElementById('main-content').classList.toggle('shift');
};

function openAddModal() {
    document.getElementById('addOrderModal').style.display = 'block';
}
function closeAddModal() {
    document.getElementById('addOrderModal').style.display = 'none';
    document.getElementById('customerName').value = '';
    document.getElementById('serviceSelect').value = '';
    document.getElementById('weightInput').value = '';
    document.getElementById('statusSelect').value = '';
    document.getElementById('dateReceived').value = '';
    document.getElementById('dueDate').value = '';
    document.getElementById('priceDisplay').textContent = '0.00';
}

window.onclick = function(event) {
    var addOrderModal = document.getElementById('addOrderModal');
    var orderModal = document.getElementById('orderModal');
    if (event.target == addOrderModal) {
        closeAddModal();
    }
    if (event.target == orderModal) {
        closeOrderModal();
    }
};

function addOrderToTable(order) {
    const tbody = document.getElementById('orders-body');
    const tr = document.createElement('tr');
    tr.setAttribute('data-order-id', order.order_id);
    tr.innerHTML = `
        <td>${order.order_id || ''}</td>
        <td>${order.customer}</td>
        <td>${order.service}</td>
        <td><span class='status ${order.status}'>${capitalize(order.status.replace(/-/g, ' '))}</span></td>
        <td>${order.date}</td>
        <td>${order.due}</td>
        <td>₱${parseFloat(order.price).toFixed(2)}</td>
        <td>
            <button class="btn-view" data-order-id="${order.order_id}">View</button>
            <button class="btn-edit" data-order-id="${order.order_id}">Edit</button>
            <button class="btn-delete" data-order-id="${order.order_id}">Delete</button>
        </td>
    `;
    tbody.prepend(tr); 
    attachActionListeners(tr);
}

function capitalize(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

document.getElementById('logout-btn').addEventListener('click', function() {
    document.getElementById('logout-box').style.display =
    document.getElementById('logout-box').style.display === 'block' ? 'none' : 'block';
});
window.addEventListener('click', function(event) {
    const logoutBox = document.getElementById('logout-box');
    const logoutBtn = document.getElementById('logout-btn');
    if (event.target !== logoutBox && event.target !== logoutBtn && !logoutBtn.contains(event.target)) {
        logoutBox.style.display = 'none';
    }
});
const logoutBoxElement = document.getElementById('logout-box');
if (logoutBoxElement) {
    logoutBoxElement.onclick = function(e) {
        e.stopPropagation();
    };
}

</script>
</body>
</html>
