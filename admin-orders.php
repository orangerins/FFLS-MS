<?php
    // Include database connection
    include 'db_connection.php';

    // Initialize variables
    $result = null;
    $error = null;

    // Fetch orders data from the database
    $sql = "SELECT
                orders.order_id,
                orders.user_id,
                users.first_name,
                users.last_name,
                orders.date_placed,
                orders.status,
                orders.total_price,
                orders.weight,
                orders.date_created
            FROM orders
            LEFT JOIN users ON orders.user_id = users.user_id
            ORDER BY orders.date_placed DESC";

    $result = $conn->query($sql);

    if (!$result) {
        $error = "Query failed: " . $conn->error;
    }

    // Close database connection
    $conn->close();
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Orders</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
        <style>
            /* Reset and general styles */
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

            /* Header styles */
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

            /* Sidebar styles */
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

            /* Content area styles */
            .content {
                margin-left: 20px;
                margin-top: 80px;
                padding: 20px;
                transition: margin-left 0.3s ease-in-out;
                overflow-y: auto;
                height: calc(100vh - 80px);
                display: flex; /* Add flex display to arrange items horizontally */
                flex-direction: column; /* Stack items vertically by default */
            }

            .content.shift {
                margin-left: 260px;
            }

            h1 {
                margin-bottom: 20px;
                color: #333;
            }

            /* Search bar styles - Adjusted for Inventory look */
            #search-container {
                margin-bottom: 20px;
                display: flex;
                justify-content: flex-start; /* Align to the left */
                align-items: center;
                width: 300px; /* Set a fixed width like the Inventory page */
            }

            #search-input {
                padding: 8px 15px;
                width: 100%;
                border: 1px solid #ddd;
                border-radius: 4px;
            }

            /* Table styles */
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

            /* Status select styles */
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
            <div class="user-profile" id="logout-area">
                <span id="admin-username" style="cursor: pointer;">Admin</span>
                <i class="fas fa-user-circle" id="profile-icon"></i>
                <div class="logout-box" id="logout-box">
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

        <div class="content">
            <h1>Orders</h1>

            <div id="search-container">
                <input type="text" id="search-input" placeholder="Search Orders...">
            </div>

            <?php if ($error): ?>
                <div class="error-message" style="color: red; padding: 10px; background: #ffeeee; border: 1px solid #ffcccc; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <table id="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User ID</th>
                        <th>User Name</th>
                        <th>Date Placed</th>
                        <th>Status</th>
                        <th>Total Price</th>
                        <th>Weight (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date_placed']) . "</td>";
                            echo "<td>";
                            echo "<select class='status-select' data-order-id='" . htmlspecialchars($row['order_id']) . "'>";
                            $statuses = ['Pending', 'Washing', 'Drying', 'Folding', 'Ready for Pickup', 'Completed', 'Cancelled'];
                            foreach ($statuses as $status) {
                                $selected = ($row['status'] == $status) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($status) . "' " . $selected . ">" . htmlspecialchars($status) . "</option>";
                            }
                            echo "</select>";
                            echo "</td>";
                            echo "<td>$" . number_format($row['total_price'], 2) . "</td>";
                            echo "<td>" . number_format($row['weight'], 2) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>" . ($error ? "Error loading orders" : "No orders found") . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script>
            $(document).ready(function() {
                // Sidebar toggle functionality
                $('#menu-btn').click(function() {
                    console.log('Menu button clicked!'); // Debugging line
                    $('#sidebar').toggleClass('active');
                    $('.content').toggleClass('shift');
                });

                // Live search functionality
                $('#search-input').on('input', function() {
                    const searchText = $(this).val().toLowerCase();
                    $('#orders-table tbody tr').filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                    });
                });

                // Status update functionality
                $(document).on('change', '.status-select', function() {
                    const orderId = $(this).data('order-id');
                    const newStatus = $(this).val();

                    $.post('update_order_status.php', { order_id: orderId, status: newStatus })
                        .done(function(response) {
                            if (response === 'success') {
                                alert('Order status updated successfully.');
                            } else {
                                alert('Failed to update order status.');
                            }
                        })
                        .fail(function() {
                            alert('Error: Could not connect to server.');
                        });
                });

                // Logout box toggle
                $('#admin-username').click(function() {
                    $('#logout-box').toggle();
                });

                // Close logout box when clicking outside
                $(document).on('click', function(event) {
                    if (!$(event.target).closest('#logout-area').length) {
                        $('#logout-box').hide();
                    }
                });
            });
        </script>
    </body>
    </html>
