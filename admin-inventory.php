<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Admin Inventory</title>
        </head>
    <body>
        <script>
            const initialCsrfToken = "<?php echo $_SESSION['csrf_token']; ?>";
        </script>
    </body>
    </html>
    <?php
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF token validation failed for admin-inventory.php", 0);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'errors' => ["CSRF token validation failed"]]);
        exit;
    }
    unset($_SESSION['csrf_token']);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    if (isset($_POST['add_item'])) {
        $itemName = isset($_POST['item_name']) ? trim($_POST['item_name']) : '';
        $category = isset($_POST['category']) ? trim($_POST['category']) : '';
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
        $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00;

        $errors = [];
        if (strlen($itemName) < 2 || strlen($itemName) > 255) {
            $errors[] = "Item Name must be between 2 and 255 characters.";
        }
        if (strlen($category) > 1000) {
            $errors[] = "Category cannot exceed 1000 characters.";
        }
        if ($quantity < 0) {
            $errors[] = "Quantity must be a non-negative number.";
        }
        if ($price < 0) {
            $errors[] = "Price must be a non-negative number.";
        }

        $allowedCategories = ["Cleaning Supplies", "Packaging", "Equipment", "Other"];
        if (!in_array($category, $allowedCategories)) {
            $errors[] = "Invalid category selected.";
        }

        if (!empty($errors)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO inventory (item_name, category, quantity, price) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssii", $itemName, $category, $quantity, $price);
            if ($stmt->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Item added successfully!']);
            } else {
                error_log("Database error (add_item): " . $stmt->error, 0);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'errors' => ["Error adding item: " . $stmt->error]]);
            }
            $stmt->close();
        } else {
            error_log("Database error (prepare add_item): " . $conn->error, 0);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'errors' => ["Database error"]]);
        }

    } elseif (isset($_POST['update_item'])) {
        $itemId = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
        $itemName = isset($_POST['item_name']) ? trim($_POST['item_name']) : '';
        $category = isset($_POST['category']) ? trim($_POST['category']) : '';
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
        $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00;

        $errors = [];
        if ($itemId <= 0) {
            $errors[] = "Invalid Item ID.";
        }
        if (strlen($itemName) < 2 || strlen($itemName) > 255) {
            $errors[] = "Item Name must be between 2 and 255 characters.";
        }
        if (strlen($category) > 1000) {
            $errors[] = "Category cannot exceed 1000 characters.";
        }
        if ($quantity < 0) {
            $errors[] = "Quantity must be a non-negative number.";
        }
        if ($price < 0) {
            $errors[] = "Price must be a non-negative number.";
        }

        $allowedCategories = ["Cleaning Supplies", "Packaging", "Equipment", "Other"];
        if (!in_array($category, $allowedCategories)) {
            $errors[] = "Invalid category selected.";
        }

        if (!empty($errors)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            exit;
        }

        $stmt = $conn->prepare("UPDATE inventory SET item_name=?, category=?, quantity=?, price=? WHERE item_id=?");
        if ($stmt) {
            $stmt->bind_param("ssidi", $itemName, $category, $quantity, $price, $itemId);
            if ($stmt->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Item updated successfully!']);
            } else {
                error_log("Database error (update_item): " . $stmt->error, 0);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'errors' => ["Error updating item: " . $stmt->error]]);
            }
            $stmt->close();
        } else {
            error_log("Database error (prepare update_item): " . $conn->error, 0);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'errors' => ["Database error"]]);
        }

    } elseif (isset($_POST['delete_item'])) {
        $itemId = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;

        $errors = [];
        if ($itemId <= 0) {
            $errors[] = "Invalid Item ID.";
        }

        if (!empty($errors)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM inventory WHERE item_id=?");
        if ($stmt) {
            $stmt->bind_param("i", $itemId);
            if ($stmt->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Item deleted successfully!']);
            } else {
                error_log("Database error (delete_item): " . $stmt->error, 0);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'errors' => ["Error deleting item: " . $stmt->error]]);
            }
            $stmt->close();
        } else {
            error_log("Database error (prepare delete_item): " . $conn->error, 0);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'errors' => ["Database error"]]);
        }

    } elseif (isset($_POST['decrease_item'])) {
        $itemId = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
        $decreaseQuantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

        $errors = [];
        if ($itemId <= 0 || $decreaseQuantity <= 0) {
            $errors[] = "Invalid input.";
        }

        if (!empty($errors)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'errors' => $errors]);
            exit;
        }

        $stmt = $conn->prepare("UPDATE inventory SET quantity = GREATEST(0, quantity - ?) WHERE item_id = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $decreaseQuantity, $itemId);
            if ($stmt->execute()) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Quantity decreased.']);
            } else {
                error_log("Database error (decrease): " . $stmt->error, 0);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'errors' => ["Error decreasing quantity."]]);
            }
            $stmt->close();
        } else {
            error_log("Database error (prepare decrease): " . $conn->error, 0);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'errors' => ["Database error"]]);
        }
    }
}

$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);
$inventory_items = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $item = [
            'item_id' => intval($row['item_id']),
            'item_name' => htmlspecialchars($row['item_name'], ENT_QUOTES, 'UTF-8'),
            'category' => htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8'),
            'quantity' => intval($row['quantity']),
            'price' => floatval($row['price']),
            'status' => getStatusText(intval($row['quantity']))
        ];
        $inventory_items[] = $item;
    }
}

$conn->close();

function getStatusText($quantity) {
    if ($quantity == 0) return "Out of Stock";
    if ($quantity < 10) return "Low Stock";
    return "In Stock";
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
        
        <table>
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventory_items as $item): ?>
                    <tr>
                        <td><?= $item['item_id'] ?></td>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= htmlspecialchars($item['category']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td class="status-<?= strtolower(str_replace(' ', '-', $item['status'])) ?>">
                            <?= $item['status'] ?>
                        </td>
                        <td>
                            <form class="decrease-form" method="POST" action="admin-inventory.php">
                                <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                <input type="number" class="decrease-input" name="quantity" value="1" min="1" max="<?= $item['quantity'] ?>">
                                <button type="submit" name="decrease_item">Decrease</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
