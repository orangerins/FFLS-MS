<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $itemId = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $itemName = isset($_POST['item_name']) ? mysqli_real_escape_string($conn, $_POST['item_name']) : '';
    $category = isset($_POST['category']) ? mysqli_real_escape_string($conn, $_POST['category']) : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00;

    if ($itemId <= 0 || empty($itemName)) {
        echo "error: Invalid item ID or name.";
        exit();
    }

    // Determine the new status based on the updated quantity
    $status = ($quantity > 0) ? 'In Stock' : 'Out of Stock';

    $sql = "UPDATE inventory SET item_name = ?, category = ?, quantity = ?, price = ?, status = ? WHERE item_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssidsi", $itemName, $category, $quantity, $price, $status, $itemId);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "error: " . $conn->error;
    }
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'view') {
    $itemId = isset($_GET['item_id']) ? intval($_GET['item_id']) : 0;
    if ($itemId > 0) {
        $sql = "SELECT item_id, item_name, category, quantity, price FROM inventory WHERE item_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $itemId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                echo json_encode($row);
            } else {
                echo json_encode(null);
            }
            $stmt->close();
        } else {
            echo "error: " . $conn->error;
        }
    } else {
        echo json_encode(null);
    }
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    $itemId = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    if ($itemId > 0) {
        $sql = "DELETE FROM inventory WHERE item_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $itemId);
            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "error: " . $conn->error;
        }
    } else {
        echo "error: Invalid item ID.";
    }
    exit();
}
?>