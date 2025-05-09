<?php
include 'db_connection.php';

// Handle adding new items and updating existing ones
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_item'])) {
    $itemId = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $itemName = isset($_POST['item_name']) ? mysqli_real_escape_string($conn, $_POST['item_name']) : '';
    $category = isset($_POST['category']) ? mysqli_real_escape_string($conn, $_POST['category']) : '';
    $initialQuantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0; // For new items
    $addQuantity = isset($_POST['add_quantity']) ? intval($_POST['add_quantity']) : 0;
    $subtractQuantity = isset($_POST['subtract_quantity']) ? intval($_POST['subtract_quantity']) : 0;
    $currentQuantityHidden = isset($_POST['current_quantity_hidden']) ? intval($_POST['current_quantity_hidden']) : 0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00;

    if (empty($itemName)) {
        echo "Error: Item name is required.";
        exit();
    }

    if ($itemId > 0) {
        // Updating an existing item
        $newQuantity = $currentQuantityHidden + $addQuantity - $subtractQuantity;

        $updateSql = "UPDATE inventory SET item_name = ?, category = ?, quantity = ?, price = ? WHERE item_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        if ($updateStmt) {
            $updateStmt->bind_param("ssidi", $itemName, $category, $newQuantity, $price, $itemId);
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
        // Adding a new item - Check if item name already exists
        $checkSql = "SELECT item_id, quantity FROM inventory WHERE item_name = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $itemName);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Item with the same name exists, update its quantity
            $existingItem = $checkResult->fetch_assoc();
            $newQuantity = $existingItem['quantity'] + $initialQuantity;
            $updateExistingSql = "UPDATE inventory SET quantity = ? WHERE item_id = ?";
            $updateExistingStmt = $conn->prepare($updateExistingSql);
            $updateExistingStmt->bind_param("ii", $newQuantity, $existingItem['item_id']);
            if ($updateExistingStmt->execute()) {
                echo "success";
            } else {
                echo "error: " . $updateExistingStmt->error;
            }
            $updateExistingStmt->close();
        } else {
            // Item with the same name does not exist, insert as a new item
            $insertSql = "INSERT INTO inventory (item_name, category, quantity, price) VALUES (?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            if ($insertStmt) {
                $insertStmt->bind_param("ssdi", $itemName, $category, $initialQuantity, $price);
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
                echo "success";
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
$sql = "SELECT item_id, item_name, category, quantity, price FROM inventory";
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

        .action-link.subtract {
            color: #007bff; /* Blue color for subtract */
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
            background: rgba(0, 123, 255, 0.3); /* Slightly translucent blue */
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
            border-radius: 4px;
        }

        .subtract-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <img src="ad-icon.png" alt="Admin Icon">
            <div class="logout-box"id="logout-box">
                <a href="login.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="sidebar" id="sidebar">
        <ul>
            <li><a href="admin-dashboard.php"><img src="d-icon.png"></i> Dashboard</a></li>
            <li><a href="admin-orders.php"><img src="O-icon.png"></i> Orders</a></li>
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
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inventoryItems)): ?>
                    <tr><td colspan='6'>No inventory items found</td></tr>
                <?php else: ?>
                    <?php foreach ($inventoryItems as $item): ?>
                        <tr id="row-<?php echo htmlspecialchars($item['item_id']); ?>" data-item='<?php echo htmlspecialchars(json_encode($item)); ?>'>
                            <td><?php echo htmlspecialchars(sprintf('%03d', $item["item_id"])); ?></td>
                            <td><?php echo htmlspecialchars($item["item_name"]); ?></td>
                            <td><?php echo htmlspecialchars($item["category"]); ?></td>
                            <td><?php echo htmlspecialchars($item["quantity"]); ?></td>
                            <td>₱<?php echo htmlspecialchars(number_format($item["price"], 2)); ?></td>
                            <td>
                                <div class="action-links">
                                    <span class='action-link' onclick='openEditItemModal(<?php echo htmlspecialchars($item["item_id"]); ?>)'>Edit</span>
                                    <span class='action-link delete' onclick='confirmDeleteItem(<?php echo htmlspecialchars($item["item_id"]); ?>, "<?php echo htmlspecialchars($item["item_name"]); ?>")'>Delete</span>
                                </div>
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
                    <div id="quantity-fields">
                        <div class="form-group" id="initial-quantity-group">
                            <label for="initial-quantity">Initial Quantity</label>
                            <input type="number" id="initial-quantity" name="quantity" value="0" min="0">
                        </div>
                        <div class="form-group" id="adjust-quantity-group" style="display: none;">
                            <label for="add-quantity">Add Quantity</label>
                            <input type="number" id="add-quantity" name="add_quantity" value="0" min="0">
                        </div>
                        <div class="form-group" id="subtract-quantity-group" style="display: none;">
                            <label for="subtract-quantity">Subtract Quantity</label>
                            <input type="number" id="subtract-quantity" name="subtract_quantity" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label for="current-quantity-display">Current Quantity</label>
                            <input type="number" id="current-quantity-display" value="0" readonly>
                            <input type="hidden" id="current-quantity" name="current_quantity_hidden" value="0">
                        </div>
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

        <script>
            let itemToDeleteId = null;
            let addEditItemModal;
            let modalTitle;
            let itemIdInput;
            let itemNameInput;
            let categorySelect;
            let initialQuantityGroup;
            let initialQuantityInput;
            let adjustQuantityGroup;
            let addQuantityInput;
            let subtractQuantityGroup;
            let subtractQuantityInput;
            let currentQuantityDisplayInput;
            let currentQuantityHiddenInput;
            let priceInput;
            let deleteItemModal;
            let deleteItemNameSpan;

            document.addEventListener('DOMContentLoaded', function() {
                addEditItemModal = document.getElementById('add-edit-item-modal');
                modalTitle = document.getElementById('modal-title');
                itemIdInput = document.getElementById('item-id');
                itemNameInput = document.getElementById('item-name');
                categorySelect = document.getElementById('category');
                initialQuantityGroup = document.getElementById('initial-quantity-group');
                initialQuantityInput = document.getElementById('initial-quantity');
                adjustQuantityGroup = document.getElementById('adjust-quantity-group');
                addQuantityInput = document.getElementById('add-quantity');
                subtractQuantityGroup = document.getElementById('subtract-quantity-group');
                subtractQuantityInput = document.getElementById('subtract-quantity');
                currentQuantityDisplayInput = document.getElementById('current-quantity-display');
                currentQuantityHiddenInput = document.getElementById('current-quantity');
                priceInput = document.getElementById('price');
                deleteItemModal = document.getElementById('delete-item-modal');
                deleteItemNameSpan = document.getElementById('delete-item-name');

                document.getElementById('menu-btn').addEventListener('click', function() {
                    document.getElementById('sidebar').classList.toggle('active');
                    document.getElementById('mainContent').classList.toggle('shift');
                });

                document.getElementById('logout-btn').addEventListener('click', function() {
                    document.getElementById('logout-box').style.display =
                        document.getElementById('logout-box').style.display === 'block' ? 'none' : 'block';
                });

                if (priceInput) {
                    priceInput.addEventListener('input', preventNegativeInput);
                }
                if (initialQuantityInput) {
                    initialQuantityInput.addEventListener('input', preventNegativeInput);
                }
                if (addQuantityInput) {
                    addQuantityInput.addEventListener('input', preventNegativeInput);
                }
                if (subtractQuantityInput) {
                    subtractQuantityInput.addEventListener('input', preventNegativeInput);
                }

                document.getElementById('add-edit-item-form').addEventListener('submit', function(event) {
                    event.preventDefault();

                    const formData = new FormData(this);
                    const itemId = parseInt(itemIdInput.value);
                    let finalQuantity;

                    if (itemId > 0) {
                        const currentQuantity = parseInt(currentQuantityHiddenInput.value);
                        const addQuantity = parseInt(addQuantityInput.value);
                        const subtractQuantity = parseInt(subtractQuantityInput.value);
                        finalQuantity = currentQuantity + addQuantity - subtractQuantity;
                        formData.set('quantity', finalQuantity); // Update with the calculated final quantity
                    } else {
                        finalQuantity = parseInt(initialQuantityInput.value);
                        formData.set('quantity', finalQuantity); // Use initial quantity for new items
                    }

                    fetch('admin-inventory.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data === 'success') {
                            alert(itemId === 0 ? 'Item added successfully!' : 'Item updated successfully!');
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
            });

            function openAddItemModal() {
                modalTitle.textContent = 'Add New Item';
                itemIdInput.value = 0;
                itemNameInput.value = '';
                categorySelect.value = '';
                initialQuantityGroup.style.display = 'block';
                adjustQuantityGroup.style.display = 'none';
                subtractQuantityGroup.style.display = 'none';
                currentQuantityDisplayInput.value = 0;
                currentQuantityHiddenInput.value = 0;
                priceInput.value = '';
                addEditItemModal.style.display = 'flex';
            }

            function openEditItemModal(id) {
                console.log("openEditItemModal called with ID:", id);
                const row = document.getElementById(`row-${id}`);
                if (row) {
                    const itemData = JSON.parse(row.dataset.item);

                    modalTitle.textContent = 'Edit Item';
                    itemIdInput.value = itemData.item_id;
                    itemNameInput.value = itemData.item_name;
                    categorySelect.value = itemData.category;
                    initialQuantityGroup.style.display = 'none';
                    adjustQuantityGroup.style.display = 'block';
                    subtractQuantityGroup.style.display = 'block';
                    currentQuantityDisplayInput.value = itemData.quantity;
                    currentQuantityHiddenInput.value = itemData.quantity;
                    addQuantityInput.value = 0;
                    subtractQuantityInput.value = 0;
                    priceInput.value = itemData.price;
                    addEditItemModal.style.display = 'flex';
                } else {
                    alert('Item data not found.');
                }
            }

            function preventNegativeInput(event) {
                if (event.target.type === 'number' && event.target.value < 0) {
                    event.target.value = 0; // Prevent negative input for all number fields related to quantity and price
                }
            }

            function confirmDeleteItem(id, itemName) {
                itemToDeleteId = id;
                deleteItemNameSpan.textContent = itemName;
                deleteItemModal.style.display = 'flex';
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
