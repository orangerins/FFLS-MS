<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id']) && isset($_POST['quantity_to_subtract'])) {
    $itemId = intval($_POST['item_id']);
    $quantityToSubtract = intval($_POST['quantity_to_subtract']);

    if ($itemId > 0 && $quantityToSubtract > 0) {
        $sql = "UPDATE inventory SET quantity = quantity - ? WHERE item_id = ? AND quantity >= ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $quantityToSubtract, $itemId, $quantityToSubtract);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "success"; // Inventory updated successfully
            } else {
                echo "error: Not enough stock or item not found.";
            }
        } else {
            echo "error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "error: Invalid item ID or quantity.";
    }
    exit();
} else {
    echo "error: Invalid request.";
}
?>