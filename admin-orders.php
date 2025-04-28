<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FreshFold Orders</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
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
      color: white; /* Updated to white text */
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    /* Sidebar icon sizing */
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
    .btn-group button {
      margin-right: 5px;
      padding: 6px 10px;
      border: none;
      border-radius: 4px;
      color: white;
      cursor: pointer;
    }
    .btn-view { background-color: #28a745; }
    .btn-edit { background-color: #007bff; }
    .btn-delete { background-color: #dc3545; }
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
    /* Modals */
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
    .modal-content p strong {
      font-weight: bold;
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
    /* Right aligned menu icon */
    
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
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="orders-body"></tbody>
  </table>
</div>

  <!-- ===== Modals ===== -->

  <!-- View Order Modal -->
  <!-- View Modal -->
<div id="viewModal" class="modal">
  <div class="modal-content">
    <h3>Order Details</h3>
    <p><strong>Order #:</strong> <span id="v-id"></span></p>
    <p><strong>Customer:</strong> <span id="v-customer"></span></p>
    <p><strong>Service:</strong> <span id="v-service"></span></p>
    <p><strong>Weight:</strong> <span id="v-weight"></span> kg</p>
    <p><strong>Status:</strong> <span id="v-status" class="status"></span></p>
    <p id="sub-washing" class="sub-status">Washing: Pending</p>
    <p id="sub-drying" class="sub-status">Drying: Pending</p>
    <p id="sub-folding" class="sub-status">Folding: Pending</p>
    <p><strong>Date Received:</strong> <span id="v-date"></span></p>
    <p><strong>Due Date:</strong> <span id="v-due"></span></p>
    <p><strong>Payment:</strong> ₱<span id="v-payment"></span></p>
    <button onclick="closeModal('viewModal')">Close</button>
  </div>
</div>

  <!-- Add Order Modal -->
  <div id="addModal" class="modal">
    <div class="modal-content">
      <h3>Add New Order</h3>
      <input id="a-customer" type="text" placeholder="Customer Name">

    <select id="a-service" onchange="updatePayment()">
      <option value="Wash Only">Wash Only</option>
      <option value="Full Service">Full Service (Wash+Dry+Fold)</option>
    </select>

    <input id="a-weight" type="number" min="1" placeholder="Weight (kg)" oninput="updatePayment()">

    <select id="a-status">
      <option>Pending</option>
      <option>Washing</option>
      <option>Drying</option>
      <option>Folding</option>
      <option>Ready for Pick Up</option>
      <option>Completed</option>
    </select>

    <input id="a-date" type="date">
    <input id="a-due" type="date">

    <p><strong>Total Payment: ₱<span id="a-payment">0.00</span></strong></p>

    <button class="btn-save" onclick="addOrder()">Save</button>
    <button class="btn-cancel" onclick="closeModal('addModal')">Cancel</button>

    </div>
  </div>

  <!-- Edit Order Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <h3>Edit Order <span id="e-id"></span></h3>
      <input id="e-customer" type="text" placeholder="Customer Name">
      <input id="e-service" type="text" placeholder="Service">
      <select id="e-status">
        <option>Pending</option>
        <option>Washing</option>
        <option>Drying</option>
        <option>Folding</option>
        <option>Ready for Pick Up</option>
        <option>Completed</option>
      </select>
      <input id="e-date" type="date">
      <input id="e-due" type="date">
      <button class="btn-save" onclick="saveEdit()">Save</button>
      <button class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
    </div>
  </div>

  <!-- ===== Scripts ===== -->
  <script>
    let orderData = {
      '001': {
        customer: 'Jay Xyz',
        service: 'Wash Only',
        status: 'Pending',
        date: '2025-03-20',
        due: '2025-03-30',
        weight: 8,
        payment: '50.00'
      }
    };

    let editingId = null;

    // Render table rows
    function renderOrders() {
      const tbody = document.getElementById('orders-body');
      tbody.innerHTML = '';
      for (const id in orderData) {
        const o = orderData[id];
        const cls = o.status === 'Completed' ? 'completed'
                  : o.status === 'Ready for Pick Up' ? 'pickup'
                  : 'pending';
        tbody.insertAdjacentHTML('beforeend', `
          <tr id="order-${id}">
            <td>${id}</td>
            <td>${o.customer}</td>
            <td>${o.service}</td>
            <td class="status ${cls}">${o.status}</td>
            <td>${o.date}</td>
            <td>${o.due}</td>
            <td class="btn-group">
              <button class="btn-view" onclick="viewOrder('${id}')">View</button>
              <button class="btn-edit" onclick="openEditModal('${id}')">Edit</button>
              <button class="btn-delete" onclick="deleteOrder('${id}')">Delete</button>
            </td>
          </tr>`);
      }
    }

    // Open any modal
    function openModal(id) {
      document.getElementById(id).style.display = 'block';
    }
    // Close any modal
    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    // VIEW
    function viewOrder(id) {
  const o = orderData[id];
  if (!o) return alert("Order not found.");

  document.getElementById('v-id').textContent = id;
  document.getElementById('v-customer').textContent = o.customer;
  document.getElementById('v-service').textContent = o.service;

  const weight = o.weight ?? 0;
  const payment = o.payment ?? calculatePayment(o.service, weight);

  document.getElementById('v-weight').textContent = weight + ' kg';
  document.getElementById('v-payment').textContent = parseFloat(payment).toFixed(2);

  const st = document.getElementById('v-status');
  st.textContent = o.status;
  st.className = 'status ' + (
    o.status === 'Completed' ? 'completed' :
    o.status === 'Ready for Pick Up' ? 'pickup' :
    'pending'
  );

  // Sub-status logic using updated IDs
  document.getElementById('sub-washing').textContent =
    'Washing: ' + (o.status === 'Washing' ? 'In Progress' :
    ['Drying', 'Folding', 'Ready for Pick Up', 'Completed'].includes(o.status) ? 'Done' : 'Pending');

  document.getElementById('sub-drying').textContent =
    'Drying: ' + (o.status === 'Drying' ? 'In Progress' :
    ['Folding', 'Ready for Pick Up', 'Completed'].includes(o.status) ? 'Done' : 'Pending');

  document.getElementById('sub-folding').textContent =
    'Folding: ' + (o.status === 'Folding' ? 'In Progress' :
    ['Ready for Pick Up', 'Completed'].includes(o.status) ? 'Done' : 'Pending');

  document.getElementById('v-date').textContent = o.date;
  document.getElementById('v-due').textContent = o.due;

  openModal('viewModal');
}


    function openAddModal() {
      // clear fields
      ['a-customer','a-service','a-status','a-date','a-due'].forEach(id => {
        const el = document.getElementById(id);
        if(el.tagName==='SELECT') el.selectedIndex = 0;
        else el.value = '';
      });
      openModal('addModal');
    }
    function addOrder() {
      const cust = document.getElementById('a-customer').value.trim();
      const serv = document.getElementById('a-service').value;
      const weight = parseFloat(document.getElementById('a-weight').value);
      const stat = document.getElementById('a-status').value;
      const dt = document.getElementById('a-date').value;
      const du = document.getElementById('a-due').value;

      if (!cust || !serv || isNaN(weight) || !dt || !du) {
        return alert("Please fill all fields including weight.");
      }

      const id = String(Object.keys(orderData).length + 1).padStart(3, '0');
      const payment = calculatePayment(serv, weight);

      orderData[id] = {
        customer: cust,
        service: serv,
        weight,
        status: stat,
        date: dt,
        due: du,
        payment
      };

      renderOrders();
      closeModal('addModal');
    }


    function openEditModal(id) {
      editingId = id;
      const o = orderData[id];
      document.getElementById('e-id').textContent = `#${id}`;
      document.getElementById('e-customer').value = o.customer;
      document.getElementById('e-service').value = o.service;
      document.getElementById('e-status').value = o.status;
      document.getElementById('e-date').value = o.date;
      document.getElementById('e-due').value = o.due;
      openModal('editModal');
    }
    function saveEdit() {
      const cust = document.getElementById('e-customer').value.trim();
      const serv = document.getElementById('e-service').value.trim();
      const stat = document.getElementById('e-status').value;
      const dt   = document.getElementById('e-date').value;
      const du   = document.getElementById('e-due').value;
      if(!cust||!serv||!dt||!du) return alert('Please fill all fields.');
      orderData[editingId] = { customer: cust, service: serv, status: stat, date: dt, due: du };
      renderOrders();
      closeModal('editModal');
    }

 
    function deleteOrder(id) {
      if(confirm(`Delete order #${id}?`)) {
        delete orderData[id];
        renderOrders();
      }
    }

    function calculatePayment(service, weight) {
    const rate = service === "Full Service" ? 22.5 : 6.25;
      return (rate * weight).toFixed(2);
    }

    function updatePayment() {
    const service = document.getElementById("a-service").value;
    const weight = parseFloat(document.getElementById("a-weight").value) || 0;
    const total = calculatePayment(service, weight);
    document.getElementById("a-payment").textContent = total;
    }



    // SEARCH
    document.getElementById('order-search').addEventListener('input', e => {
      const term = e.target.value.toLowerCase();
      document.querySelectorAll('#orders-body tr').forEach(row => {
        const [oid, cust] = [row.cells[0].textContent, row.cells[1].textContent].map(t=>t.toLowerCase());
        row.style.display = (oid.includes(term)||cust.includes(term)) ? '' : 'none';
      });
    });

    // MENU & LOGOUT
    document.getElementById('menu-btn').onclick = () => {
      document.getElementById('sidebar').classList.toggle('active');
      document.getElementById('main-content').classList.toggle('shift');
    };
    document.getElementById('profile-icon').onclick = () => {
      const lb = document.getElementById('logout-box');
      lb.style.display = lb.style.display==='block' ? 'none' : 'block';
    };
    document.addEventListener('click', e => {
      if(!e.target.closest('#profile-icon')) {
        document.getElementById('logout-box').style.display = 'none';
      }
    });

    
    renderOrders();
  </script>
</body>
</html>
