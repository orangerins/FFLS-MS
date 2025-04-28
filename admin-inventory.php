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
            <div class="logout-box" id="logout-box">
                <a href="login.html">Logout</a>
            </div>
        </div>
    </header>

    <div class="sidebar" id="sidebar">
        <ul>
          <li><a href="admin-dashboard.html"><img src="d-icon.png"></i> Dashboard</a></li>
          <li class="active-menu"><a href="admin-orders.html"><img src="O-icon.png"><i class="Orders"></i> Orders</a></li>
          <li><a href="admin-customers.html"><img src="c-icon.png"></i> Customers</a></li>
          <li><a href="admin-inventory.html"><img src="i-icon.png"></i> Inventory</a></li>
          <li><a href="admin-Paymentsss.html"><img src="p-icon.png"></i> Payments</a></li>
          <li><a href="admin-reports.html"><img src="rp-icon.png"></i> Reports</a></li>
        </ul>
      </div>

    <div class="content" id="mainContent">
        <h1>Inventory</h1>
        <div class="search-bar">
            <input type="text" id="search-input" placeholder="Search by item name or category" oninput="filterItems()">
            <button class="btn btn-primary" style="margin-left: 10px;" onclick="openAddItemModal()">
                <i class="fas fa-plus"></i> Add Item
            </button>
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
                <tr>
                    <td>001</td>
                    <td>Laundry Detergent</td>
                    <td>Cleaning Supplies</td>
                    <td>24</td>
                    <td>₱120.00</td>
                    <td><span class="status-in-stock">In Stock</span></td>
                    <td>
                        <span class="action-link" onclick="viewItem(1)">View</span>
                        <span class="action-link" onclick="editItem(1)">Edit</span>
                        <span class="action-link delete" onclick="confirmDeleteItem(1)">Delete</span>
                    </td>
                </tr>
                <tr>
                    <td>002</td>
                    <td>Fabric Softener</td>
                    <td>Cleaning Supplies</td>
                    <td>15</td>
                    <td>₱85.00</td>
                    <td><span class="status-in-stock">In Stock</span></td>
                    <td>
                        <span class="action-link" onclick="viewItem(2)">View</span>
                        <span class="action-link" onclick="editItem(2)">Edit</span>
                        <span class="action-link delete" onclick="confirmDeleteItem(2)">Delete</span>
                    </td>
                </tr>
                <tr>
                    <td>003</td>
                    <td>Plastic Bags (Large)</td>
                    <td>Packaging</td>
                    <td>3</td>
                    <td>₱45.00</td>
                    <td><span class="status-low-stock">Low Stock</span></td>
                    <td>
                        <span class="action-link" onclick="viewItem(3)">View</span>
                        <span class="action-link" onclick="editItem(3)">Edit</span>
                        <span class="action-link delete" onclick="confirmDeleteItem(3)">Delete</span>
                    </td>
                </tr>
                <tr>
                    <td>004</td>
                    <td>Stain Remover</td>
                    <td>Cleaning Supplies</td>
                    <td>0</td>
                    <td>₱95.00</td>
                    <td><span class="status-out-of-stock">Out of Stock</span></td>
                    <td>
                        <span class="action-link" onclick="viewItem(4)">View</span>
                        <span class="action-link" onclick="editItem(4)">Edit</span>
                        <span class="action-link delete" onclick="confirmDeleteItem(4)">Delete</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div id="add-item-modal" class="modal">
            <div class="modal-content">
                <h2>Add New Item</h2>
                <form id="add-item-form">
                    <div class="form-group">
                        <label for="item-name">Item Name</label>
                        <input type="text" id="item-name" required>
                    </div>
                    <div class="form-group">
                        <label for="item-category">Category</label>
                        <select id="item-category" required>
                            <option value="">Select Category</option>
                            <option value="Cleaning Supplies">Cleaning Supplies</option>
                            <option value="Packaging">Packaging</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="item-quantity">Quantity</label>
                        <input type="number" id="item-quantity" required>
                    </div>
                    <div class="form-group">
                        <label for="item-price">Price (₱)</label>
                        <input type="number" id="item-price" step="0.01" required>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('add-item-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div id="view-item-modal" class="modal">
            <div class="modal-content">
                <h2>Item Details</h2>
                <div class="form-group">
                    <label>Item ID</label>
                    <p id="view-item-id"></p>
                </div>
                <div class="form-group">
                    <label>Item Name</label>
                    <p id="view-item-name"></p>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <p id="view-item-category"></p>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <p id="view-item-quantity"></p>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <p id="view-item-price"></p>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <p id="view-item-status"></p>
                </div>
                <div class="modal-actions">
                    <button class="btn btn-primary" onclick="closeModal('view-item-modal')">Close</button>
                </div>
            </div>
        </div>
        
        <div id="edit-item-modal" class="modal">
            <div class="modal-content">
                <h2>Edit Item</h2>
                <form id="edit-item-form">
                    <input type="hidden" id="edit-item-id">
                    <div class="form-group">
                        <label for="edit-item-name">Item Name</label>
                        <input type="text" id="edit-item-name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-item-category">Category</label>
                        <select id="edit-item-category" required>
                            <option value="Cleaning Supplies">Cleaning Supplies</option>
                            <option value="Packaging">Packaging</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-item-quantity">Quantity</label>
                        <input type="number" id="edit-item-quantity" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-item-price">Price (₱)</label>
                        <input type="number" id="edit-item-price" step="0.01" required>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('edit-item-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
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
            const inventoryItems = [
                {
                    id: 1,
                    name: "Laundry Detergent",
                    category: "Cleaning Supplies",
                    quantity: 24,
                    price: 120.00,
                    status: "In Stock"
                },
                {
                    id: 2,
                    name: "Fabric Softener",
                    category: "Cleaning Supplies",
                    quantity: 15,
                    price: 85.00,
                    status: "In Stock"
                },
                {
                    id: 3,
                    name: "Plastic Bags (Large)",
                    category: "Packaging",
                    quantity: 3,
                    price: 45.00,
                    status: "Low Stock"
                },
                {
                    id: 4,
                    name: "Stain Remover",
                    category: "Cleaning Supplies",
                    quantity: 0,
                    price: 95.00,
                    status: "Out of Stock"
                }
            ];
        
            let itemToDelete = null;
        
            document.getElementById('menu-btn').addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('active');
                document.getElementById('mainContent').classList.toggle('shift');
            });
        
            document.getElementById('logout-btn').addEventListener('click', function() {
                document.getElementById('logout-box').style.display = 
                    document.getElementById('logout-box').style.display === 'block' ? 'none' : 'block';
            });
        
            function openAddItemModal() {
                document.getElementById('add-item-modal').style.display = 'flex';
            }
        
            function viewItem(id) {
                const item = inventoryItems.find(i => i.id === id);
                document.getElementById('view-item-id').textContent = item.id.toString().padStart(3, '0');
                document.getElementById('view-item-name').textContent = item.name;
                document.getElementById('view-item-category').textContent = item.category;
                document.getElementById('view-item-quantity').textContent = item.quantity;
                document.getElementById('view-item-price').textContent = `₱${item.price.toFixed(2)}`;
                document.getElementById('view-item-status').innerHTML = `<span class="status-${item.status.toLowerCase().replace(' ', '-')}">${item.status}</span>`;
                document.getElementById('view-item-modal').style.display = 'flex';
            }
        
            function editItem(id) {
                const item = inventoryItems.find(i => i.id === id);
                document.getElementById('edit-item-id').value = item.id;
                document.getElementById('edit-item-name').value = item.name;
                document.getElementById('edit-item-category').value = item.category;
                document.getElementById('edit-item-quantity').value = item.quantity;
                document.getElementById('edit-item-price').value = item.price;
                document.getElementById('edit-item-modal').style.display = 'flex';
            }
        
            function confirmDeleteItem(id) {
                const item = inventoryItems.find(i => i.id === id);
                itemToDelete = id;
                document.getElementById('delete-item-name').textContent = item.name;
                document.getElementById('delete-item-modal').style.display = 'flex';
            }
        
            function deleteItem() {
                if (itemToDelete) {
                    const rows = document.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        if (row.cells[0].textContent === itemToDelete.toString().padStart(3, '0')) {
                            row.remove();
                        }
                    });
                    
                    const index = inventoryItems.findIndex(item => item.id === itemToDelete);
                    if (index !== -1) {
                        inventoryItems.splice(index, 1);
                    }
                    
                    alert(`Item ${itemToDelete} deleted successfully!`);
                    closeModal('delete-item-modal');
                    itemToDelete = null;
                }
            }
        
            document.getElementById('add-item-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const newItem = {
                    id: inventoryItems.length > 0 ? Math.max(...inventoryItems.map(i => i.id)) + 1 : 1,
                    name: document.getElementById('item-name').value,
                    category: document.getElementById('item-category').value,
                    quantity: parseInt(document.getElementById('item-quantity').value),
                    price: parseFloat(document.getElementById('item-price').value),
                    status: "In Stock"
                };
                
                if (newItem.quantity === 0) {
                    newItem.status = "Out of Stock";
                } else if (newItem.quantity < 5) {
                    newItem.status = "Low Stock";
                }
                
                inventoryItems.push(newItem);
                addItemToTable(newItem);
                
                alert('New item added successfully!');
                closeModal('add-item-modal');
                this.reset();
            });
        
            document.getElementById('edit-item-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const id = parseInt(document.getElementById('edit-item-id').value);
                const itemIndex = inventoryItems.findIndex(item => item.id === id);
                
                if (itemIndex !== -1) {
                    inventoryItems[itemIndex].name = document.getElementById('edit-item-name').value;
                    inventoryItems[itemIndex].category = document.getElementById('edit-item-category').value;
                    inventoryItems[itemIndex].quantity = parseInt(document.getElementById('edit-item-quantity').value);
                    inventoryItems[itemIndex].price = parseFloat(document.getElementById('edit-item-price').value);
                    
                    if (inventoryItems[itemIndex].quantity === 0) {
                        inventoryItems[itemIndex].status = "Out of Stock";
                    } else if (inventoryItems[itemIndex].quantity < 5) {
                        inventoryItems[itemIndex].status = "Low Stock";
                    } else {
                        inventoryItems[itemIndex].status = "In Stock";
                    }
                }
                
                const rows = document.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    if (row.cells[0].textContent === id.toString().padStart(3, '0')) {
                        row.cells[1].textContent = document.getElementById('edit-item-name').value;
                        row.cells[2].textContent = document.getElementById('edit-item-category').value;
                        row.cells[3].textContent = document.getElementById('edit-item-quantity').value;
                        row.cells[4].textContent = `₱${parseFloat(document.getElementById('edit-item-price').value).toFixed(2)}`;
                        
                        const quantity = parseInt(document.getElementById('edit-item-quantity').value);
                        let status = "In Stock";
                        if (quantity === 0) {
                            status = "Out of Stock";
                        } else if (quantity < 5) {
                            status = "Low Stock";
                        }
                        row.cells[5].innerHTML = `<span class="status-${status.toLowerCase().replace(' ', '-')}">${status}</span>`;
                    }
                });
                
                alert('Item updated successfully!');
                closeModal('edit-item-modal');
            });

            function closeModal(modalId) {
                document.getElementById(modalId).style.display = 'none';
            }

            function filterItems() {
                const searchInput = document.getElementById('search-input').value.toLowerCase();
                const rows = document.querySelectorAll('#inventory-table tbody tr');

                rows.forEach(row => {
                    const itemName = row.cells[1].textContent.toLowerCase();
                    const itemCategory = row.cells[2].textContent.toLowerCase();
                    if (itemName.includes(searchInput) || itemCategory.includes(searchInput)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            function addItemToTable(item) {
                const tableBody = document.querySelector('#inventory-table tbody');
                const newRow = document.createElement('tr');
                
                newRow.innerHTML = `
                    <td>${item.id.toString().padStart(3, '0')}</td>
                    <td>${item.name}</td>
                    <td>${item.category}</td>
                    <td>${item.quantity}</td>
                    <td>₱${item.price.toFixed(2)}</td>
                    <td><span class="status-${item.status.toLowerCase().replace(' ', '-')}">${item.status}</span></td>
                    <td>
                        <span class="action-link" onclick="viewItem(${item.id})">View</span>
                        <span class="action-link" onclick="editItem(${item.id})">Edit</span>
                        <span class="action-link delete" onclick="confirmDeleteItem(${item.id})">Delete</span>
                    </td>
                `;
                
                tableBody.appendChild(newRow);
            }
        </script>
    </div>
</div>
</body>
</html>