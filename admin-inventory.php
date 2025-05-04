<?php
include 'db_connection.php';

// Handle subtracting quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subtract_item'])) {
    $itemId = intval($_POST['item_id']);
    $quantityToSubtract = intval($_POST['quantity_to_subtract']);

    if ($itemId > 0 && $quantityToSubtract > 0) {
        // Get the current quantity
        $getCurrentQuantitySql = "SELECT quantity FROM inventory WHERE item_id = ?";
        $getCurrentQuantityStmt = $conn->prepare($getCurrentQuantitySql);
        $getCurrentQuantityStmt->bind_param("i", $itemId);
        $getCurrentQuantityStmt->execute();
        $getCurrentQuantityResult = $getCurrentQuantityStmt->get_result();

        if ($getCurrentQuantityResult && $getCurrentQuantityResult->num_rows > 0) {
            $currentQuantity = $getCurrentQuantityResult->fetch_assoc()['quantity'];
            $getCurrentQuantityStmt->close();

            $newQuantity = $currentQuantity - $quantityToSubtract;

            if ($newQuantity >= 0) {
                $status = ($newQuantity > 10) ? 'In Stock' : (($newQuantity > 0) ? 'Low Stock' : 'Out of Stock');

                $sql = "UPDATE inventory SET quantity = ?, status = ? WHERE item_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isi", $newQuantity, $status, $itemId);

                if ($stmt->execute()) {
                    echo "success"; // Inventory updated successfully
                } else {
                    echo "error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "error: Cannot subtract more than the current stock.";
            }
        } else {
            echo "error: Item not found.";
        }
    } else {
        echo "error: Invalid item ID or quantity to subtract.";
    }
    exit();
}

// Handle adding new items and updating existing ones
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_item'])) {
    $itemId = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $itemName = isset($_POST['item_name']) ? mysqli_real_escape_string($conn, $_POST['item_name']) : '';
    $category = isset($_POST['category']) ? mysqli_real_escape_string($conn, $_POST['category']) : '';
    $quantityToAdd = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00;

    if (empty($itemName)) {
        echo "Error: Item name is required.";
        exit();
    }

    if ($itemId > 0) {
        // Updating an existing item (for name, category, price, adding stock)
        $currentQuantitySql = "SELECT quantity FROM inventory WHERE item_id = ?";
        $currentQuantityStmt = $conn->prepare($currentQuantitySql);
        $currentQuantityStmt->bind_param("i", $itemId);
        $currentQuantityStmt->execute();
        $currentQuantityResult = $currentQuantityStmt->get_result();
        if ($currentQuantityResult && $currentQuantityResult->num_rows > 0) {
            $currentQuantity = $currentQuantityResult->fetch_assoc()['quantity'];
            $currentQuantityStmt->close();

            $newQuantity = $currentQuantity + $quantityToAdd;
            $status = ($newQuantity > 10) ? 'In Stock' : (($newQuantity > 0) ? 'Low Stock' : 'Out of Stock');

            $updateSql = "UPDATE inventory SET item_name = ?, category = ?, quantity = ?, price = ?, status = ? WHERE item_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            if ($updateStmt) {
                $updateStmt->bind_param("ssidsi", $itemName, $category, $newQuantity, $price, $status, $itemId);
                if ($updateStmt->execute()) {
                    echo "success"; // Item updated successfully
                } else {
                    echo "error: " . $updateStmt->error;
                }
                $updateStmt->close();
            } else {
                echo "error: " . $conn->error;
            }
        } else {
            echo "error: Item not found.";
        }
    } else {
        // Adding a new item - Check if it already exists
        $checkSql = "SELECT item_id, quantity, price FROM inventory WHERE item_name = ? AND category = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ss", $itemName, $category);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Item exists, update the quantity (only adding here)
            $row = $checkResult->fetch_assoc();
            $existingQuantity = $row['quantity'];
            $existingItemId = $row['item_id'];
            $newQuantity = $existingQuantity + $quantityToAdd;
            $newStatus = ($newQuantity > 10) ? 'In Stock' : (($newQuantity > 0) ? 'Low Stock' : 'Out of Stock');

            $updateSql = "UPDATE inventory SET quantity = ?, status = ? WHERE item_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("isi", $newQuantity, $newStatus, $existingItemId);

            if ($updateStmt->execute()) {
                echo "success"; // Quantity updated successfully
            } else {
                echo "error: " . $updateStmt->error;
            }
            $updateStmt->close();
        } else {
            // Item does not exist, insert a new one
            $initialQuantity = $quantityToAdd;
            $status = ($initialQuantity > 10) ? 'In Stock' : (($initialQuantity > 0) ? 'Low Stock' : 'Out of Stock');
            $insertSql = "INSERT INTO inventory (item_name, category, quantity, price, status) VALUES (?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            if ($insertStmt) {
                $insertStmt->bind_param("ssids", $itemName, $category, $initialQuantity, $price, $status);
                if ($insertStmt->execute()) {
                    echo "success"; // Item added successfully
                } else {
                    echo "error: " . $insertStmt->error;
                }
                $insertStmt->close();
            } else {
                echo "error: " . $conn->error;
            }
        }
        $checkStmt->close();
    }
    exit();
}

// Handle deleting items
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item_id'])) {
    $deleteItemId = intval($_POST['delete_item_id']);
    if ($deleteItemId > 0) {
        $deleteSql = "DELETE FROM inventory WHERE item_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        if ($deleteStmt) {
            $deleteStmt->bind_param("i", $deleteItemId);
            if ($deleteStmt->execute()) {
                echo "success"; // Item deleted successfully
            } else {
                echo "error: " . $deleteStmt->error;
            }
            $deleteStmt->close();
        } else {
            echo "error: " . $conn->error;
        }
    } else {
        echo "error: Invalid item ID for deletion.";
    }
    exit();
}

// Fetch inventory data for display
$sql = "SELECT item_id, item_name, category, quantity, price, status FROM inventory";
$result = $conn->query($sql);
$inventoryItems = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $inventoryItems[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshFold Laundry - Inventory</title>
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
            color: #82b8ef;
            margin-right: 10px;
            cursor: pointer;
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
            width: 500px;
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
            color: white;
        }

        .btn-secondary {
            background: #f1f1f1;
            color: #333;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .status-in-stock {
            color: #28a745;
            font-weight: bold;
        }

        .status-low-stock {
            color: #ffc107;
            font-weight: bold;
        }

        .status-out-of-stock {
            color: #dc3545;
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

        .subtract-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1001;
        }

        .subtract-modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            max-width: 90%;
        }

        .subtract-form-group {
            margin-bottom: 15px;
        }

        .subtract-form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .subtract-form-group input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius:
            4px;
        }

        .subtract-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
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
            <li><a href="admin-orders.php"><img src="O-icon.png"><i class="Orders"></i> Orders</a></li>
            <li><a href="admin-customers.php"><img src="c-icon.png"></i> Customers</a></li>
            <li class="active-menu"><a href="admin-inventory.php"><img src="i-icon.png"></i> Inventory</a></li>
            <li><a href="admin-Paymentsss.php"><img src="p-icon.png"></i> Payments</a></li>
            <li><a href="admin-reports.php"><img src="rp-icon.png"></i> Reports</a></li>
        </ul>
    </div>

    <div class="content" id="mainContent">
        <h1>Inventory</h1>

        <div class="search-bar">
            <input type="text" id="search-input" onkeyup="filterItems()" placeholder="Search for items...">
            <button class="btn btn-primary" onclick="openAddItemModal()">Add New Item</button>
        </div>

        <table id="inventory-table">
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inventoryItems)): ?>
                    <tr><td colspan='7'>No inventory items found</td></tr>
                <?php else: ?>
                    <?php foreach ($inventoryItems as $item): ?>
                        <tr id="row-<?php echo htmlspecialchars($item['item_id']); ?>" data-item='<?php echo htmlspecialchars(json_encode($item)); ?>'>
                            <td><?php echo htmlspecialchars(sprintf('%03d', $item["item_id"])); ?></td>
                            <td><?php echo htmlspecialchars($item["item_name"]); ?></td>
                            <td><?php echo htmlspecialchars($item["category"]); ?></td>
                            <td><?php echo htmlspecialchars($item["quantity"]); ?></td>
                            <td>₱<?php echo htmlspecialchars(number_format($item["price"], 2)); ?></td>
                            <td><span class='status-<?php echo htmlspecialchars(strtolower(str_replace(' ', '-', $item["status"]))); ?>'><?php echo htmlspecialchars($item["status"]); ?></span></td>
                            <td>
                                <span class='action-link' onclick='openEditItemModal(<?php echo htmlspecialchars($item["item_id"]); ?>)'>Edit</span>
                                <span class='action-link delete' onclick='confirmDeleteItem(<?php echo htmlspecialchars($item["item_id"]); ?>, "<?php echo htmlspecialchars($item["item_name"]); ?>")'>Delete</span>
                                <span class='action-link subtract' onclick='openSubtractModal(<?php echo htmlspecialchars($item["item_id"]); ?>, <?php echo htmlspecialchars($item["quantity"]); ?>, "<?php echo htmlspecialchars($item["item_name"]); ?>")'>Subtract</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div id="add-edit-item-modal" class="modal">
            <div class="modal-content">
                <h2 id="modal-title">Add New Item</h2>
                <form id="add-edit-item-form" action="admin-inventory.php" method="POST">
                    <input type="hidden" name="save_item" value="true">
                    <input type="hidden" id="item-id" name="item_id" value="0">
                    <div class="form-group">
                        <label for="item-name">Item Name</label>
                        <input type="text" id="item-name" name="item_name" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Cleaning Supplies">Cleaning Supplies</option>
                            <option value="Packaging">Packaging</option>
                            <option value="Equipment">Equipment</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price (₱)</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('add-edit-item-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Item</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="delete-item-modal" class="modal">
            <div class="modal-content">
                <h2>Confirm Delete</h2>
                <p>Are you sure you want to delete item <span id="delete-item-name"></span>?</p>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('delete-item-modal')">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="deleteItem()">Delete</button>
                </div>
            </div>
        </div>

        <div id="subtract-item-modal" class="modal">
            <div class="modal-content">
                <h2>Subtract Stock</h2>
                <p>Subtract quantity for item: <span id="subtract-item-name"></span> (Current Stock: <span id="subtract-current-stock"></span>)</p>
                <form id="subtract-item-form" action="admin-inventory.php" method="POST">
                    <input type="hidden" name="subtract_item" value="true">
                    <input type="hidden" id="subtract-item-id" name="item_id">
                    <div class="form-group">
                        <label for="quantity-to-subtract">Quantity to Subtract</label>
                        <input type="number" id="quantity-to-subtract" name="quantity_to_subtract" min="1" required>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('subtract-item-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Subtract</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            let itemToDeleteId = null;
            let currentSubtractItemId = null;

            document.getElementById('menu-btn').addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('active');
                document.getElementById('mainContent').classList.toggle('shift');
            });

            document.getElementById('logout-btn').addEventListener('click', function() {
                document.getElementById('logout-box').style.display =
                    document.getElementById('logout-box').style.display === 'block' ? 'none' : 'block';
            });

            function openAddItemModal() {
                document.getElementById('modal-title').textContent = 'Add New Item';
                document.getElementById('item-id').value = 0;
                document.getElementById('item-name').value = '';
                document.getElementById('category').value = '';
                document.getElementById('quantity').value = '';
                document.getElementById('price').value = '';
                document.getElementById('add-edit-item-modal').style.display = 'flex';
            }

            function openEditItemModal(id) {
                const row = document.getElementById(`row-${id}`);
                if (row) {
                    const itemData = JSON.parse(row.dataset.item);
                    document.getElementById('modal-title').textContent = 'Edit Item';
                    document.getElementById('item-id').value = itemData.item_id;
                    document.getElementById('item-name').value = itemData.item_name;
                    document.getElementById('category').value = itemData.category;
                    document.getElementById('quantity').value = itemData.quantity;
                    document.getElementById('price').value = itemData.price;
                    document.getElementById('add-edit-item-modal').style.display = 'flex';
                } else {
                    alert('Item data not found.');
                }
            }

            document.getElementById('add-edit-item-form').addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(this);

                fetch('admin-inventory.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        alert(document.getElementById('item-id').value === '0' ? 'Item added successfully!' : 'Item updated successfully!');
                        closeModal('add-edit-item-modal');
                        location.reload();
                    } else {
                        alert('Error saving item: ' + data);
                    }
                })
                .catch(error => {
                    console.error('Error saving item:', error);
                    alert('An error occurred while saving the item.');
                });
            });

            function confirmDeleteItem(id, itemName) {
                itemToDeleteId = id;
                document.getElementById('delete-item-name').textContent = itemName;
                document.getElementById('delete-item-modal').style.display = 'flex';
            }

            function deleteItem() {
                if (itemToDeleteId !== null) {
                    const formData = new FormData();
                    formData.append('delete_item_id', itemToDeleteId);

                    fetch('admin-inventory.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data === 'success') {
                            const rowToDelete = document.getElementById(`row-${itemToDeleteId}`);
                            if (rowToDelete) {
                                rowToDelete.remove();
                            }
                            alert(`Item ${itemToDeleteId} deleted successfully!`);
                            closeModal('delete-item-modal');
                            itemToDeleteId = null;
                        } else {
                            alert('Failed to delete item.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            }

            function openSubtractModal(itemId, currentStock, itemName) {
                currentSubtractItemId = itemId;
                document.getElementById('subtract-item-name').textContent = itemName;
                document.getElementById('subtract-current-stock').textContent = currentStock;
                document.getElementById('subtract-item-id').value = itemId;
                document.getElementById('quantity-to-subtract').value = ''; // Clear previous value
                document.getElementById('subtract-item-modal').style.display = 'flex';
            }

            document.getElementById('subtract-item-form').addEventListener('submit', function(event) {
                event.preventDefault();

                if (currentSubtractItemId !== null) {
                    const formData = new FormData(this);
                    formData.append('item_id', currentSubtractItemId);
                    formData.append('subtract_item', true); // Add a flag to identify the subtract request

                    fetch('admin-inventory.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data === 'success') {
                            alert('Stock subtracted successfully!');
                            closeModal('subtract-item-modal');
                            location.reload(); // Reload to update the table
                        } else {
                            alert('Error subtracting stock: ' + data);
                        }
                    })
                    .catch(error => {
                        console.error('Error subtracting stock:', error);
                        alert('An error occurred while subtracting stock.');
                    });
                }
            });

            function closeModal(modalId) {
                document.getElementById(modalId).style.display = 'none';
            }

            function filterItems() {
                const searchInput = document.getElementById('search-input').value.toLowerCase();
                const rows = document.querySelectorAll('#inventory-table tbody tr');

                rows.forEach(row => {
                    const itemID = row.cells[0].textContent.toLowerCase();
                    const itemName = row.cells[1].textContent.toLowerCase();
                    const itemCategory = row.cells[2].textContent.toLowerCase();
                    if (itemID.includes(searchInput) || itemName.includes(searchInput) || itemCategory.includes(searchInput)) {
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
