<?php
ini_set('session.save_path', '/tmp');
session_start();

require_once 'db_connection.php';
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item'])) {
        // Secure input handling with prepared statements
        $stmt = $conn->prepare("INSERT INTO inventory (item_name, category, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssid", 
            $_POST['item_name'],
            $_POST['category'],
            $_POST['quantity'],
            $_POST['price']
        );
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Item added successfully!";
        } else {
            $_SESSION['error'] = "Error adding item: " . $conn->error;
        }
        header("Location: admin-inventory.php");
        exit();
    }
    elseif (isset($_POST['update_item'])) {
        $stmt = $conn->prepare("UPDATE inventory SET item_name=?, category=?, quantity=?, price=? WHERE item_id=?");
        $stmt->bind_param("ssidi",
            $_POST['item_name'],
            $_POST['category'],
            $_POST['quantity'],
            $_POST['price'],
            $_POST['item_id']
        );
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Item updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating item: " . $conn->error;
        }
        header("Location: admin-inventory.php");
        exit();
    }
    elseif (isset($_POST['delete_item'])) {
        $stmt = $conn->prepare("DELETE FROM inventory WHERE item_id=?");
        $stmt->bind_param("i", $_POST['item_id']);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Item deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting item: " . $conn->error;
        }
        header("Location: admin-inventory.php");
        exit();
    }
}

function getInventoryStatus($quantity, $min_stock_level = 5) {
    if ($quantity == 0) return "Out of Stock";
    elseif ($quantity < $min_stock_level) return "Low Stock";
    return "In Stock";
}

function decreaseInventory($item_id, $quantity, $conn) {
    $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity - ? WHERE item_id = ?");
    $stmt->bind_param("ii", $quantity, $item_id);
    return $stmt->execute();
}

// Fetch inventory items
$inventory_items = [];
$sql = "SELECT * FROM inventory";

if (isset($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    $sql = "SELECT * FROM inventory WHERE item_name LIKE ? OR category LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['status'] = getInventoryStatus($row['quantity'], $row['min_stock_level'] ?? 5);
        $inventory_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
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
    <!-- Your existing HTML structure -->
    <div class="content" id="mainContent">
        <h1>Inventory</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <!-- Rest of your HTML/PHP mix -->
    </div>
    <script>
        // Your existing JavaScript
        function decreaseInventoryOnOrder(itemId, quantity) {
            fetch('process_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ item_id: itemId, quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Inventory updated!");
                    location.reload();
                } else {
                    alert("Error: " + data.error);
                }
            });
        }
    </script>
</body>
</html>
