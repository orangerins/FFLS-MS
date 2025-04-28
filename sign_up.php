<?php
session_start();
include('db_connection.php');

// Initialize variables
$showRedirectScript = false;
$error = '';

// Redirect if already logged in
if (isset($_SESSION['client_logged_in']) || isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $first_name     = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name    = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name      = mysqli_real_escape_string($conn, $_POST['last_name']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $street         = mysqli_real_escape_string($conn, $_POST['street']);
    $barangay       = mysqli_real_escape_string($conn, $_POST['barangay']);
    $city           = mysqli_real_escape_string($conn, $_POST['city']);
    $province       = mysqli_real_escape_string($conn, $_POST['province']);
    $zip_code       = mysqli_real_escape_string($conn, $_POST['zip_code']);
    $email          = mysqli_real_escape_string($conn, $_POST['email']);
    $username       = mysqli_real_escape_string($conn, $_POST['username']);
    $password       = mysqli_real_escape_string($conn, $_POST['password']);

    // 1. Insert into user_account first
    $insert_account = "INSERT INTO user_account (username, password, account_type) VALUES ('$username', '$password', 'customer')";
    if (mysqli_query($conn, $insert_account)) {
        $account_id = mysqli_insert_id($conn);
        // 2. Insert into users with the new user_account_id
        $insert_user = "INSERT INTO users 
            (user_account_id, first_name, middle_name, last_name, contact_number, street, barangay, city, province, zip_code, email)
            VALUES 
            ('$account_id', '$first_name', '$middle_name', '$last_name', '$contact_number', '$street', '$barangay', '$city', '$province', '$zip_code', '$email')";
        
        if (mysqli_query($conn, $insert_user)) {
            $showRedirectScript = true;
        } else {
            $error = 'Error saving user information: ' . mysqli_error($conn);
            // Roll back the inserted account if user insert fails
            mysqli_query($conn, "DELETE FROM user_account WHERE user_account_id = $account_id");
        }
    } else {
        $error = 'Error creating user account: ' . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FreshFold Laundry Services - Sign Up</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      display: flex;
      min-height: 100vh;
      overflow: auto;
      flex-wrap: wrap;
    }

    .left-side {
      flex: 1;
      background: url('FFLSbg.jpg') no-repeat center center/cover;
    }

    .right-side {
      flex: 1;
      max-width: 500px;
      background-color: #d1e3ff;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px;
      overflow-y: auto;
    }

    .logo {
      width: 230px;
      margin: 20px 0;
    }

    .input-container {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 350px;
      text-align: left;
    }

    .input-container label {
      display: block;
      font-size: 14px;
      margin-bottom: 5px;
    }

    .input-container input {
      width: 100%;
      padding: 8px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .buttons {
      display: flex;
      justify-content: space-between;
    }

    .buttons button {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      text-align: center;
    }

    .signup {
      background-color: #6a9ff8;
      color: white;
    }

    .back-login {
      background-color: white;
      border: 1px solid #6a9ff8;
      color: #6a9ff8;
    }

    .back-login:hover {
      background-color: #6a9ff8;
      color: white;
    }

    @media (max-width: 768px) {
      body {
        flex-direction: column;
      }

      .left-side {
        display: none;
      }

      .right-side {
        width: 100%;
        max-width: none;
      }
    }
  </style>
</head>
<body>
<div class="left-side"></div>
<div class="right-side">
    <img src="FFLSlogo.png" class="logo" alt="Logo">
    <div class="input-container">
        <?php if ($error): ?>
            <p style="color:red; margin-bottom:10px;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" required>

            <label for="middle_name">Middle Name:</label>
            <input type="text" name="middle_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" required>

            <label for="contact_number">Contact Number:</label>
            <input type="text" name="contact_number" required>

            <label for="street">Street:</label>
            <input type="text" name="street" required>

            <label for="barangay">Barangay:</label>
            <input type="text" name="barangay" required>

            <label for="city">City:</label>
            <input type="text" name="city" required>

            <label for="province">Province:</label>
            <input type="text" name="province" required>

            <label for="zip_code">Zip Code:</label>
            <input type="text" name="zip_code" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <button class="signup" type="submit">Sign Up</button>
        </form>
    </div>
</div>

<?php if ($showRedirectScript): ?>
<script>
    alert('Registration successful! You can now login.');
    window.location.href = 'login.php';
</script>
<?php endif; ?>
</body>
</html>
