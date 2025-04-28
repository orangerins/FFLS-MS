<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM user_account 
            INNER JOIN users ON user_account.user_account_id = users.user_account_id 
            WHERE user_account.username = '$username' 
            AND user_account.password = '$password'";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['account_type'] === 'admin') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            header("Location: admin-dashboard.php");
            exit();
        } else {
            $_SESSION['client_logged_in'] = true;
            $_SESSION['client_username'] = $user['username'];
            $_SESSION['client_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['client_email'] = $user['email'];
            $_SESSION['client_address'] = $user['street'];
            header("Location: Dashboard-Client.php");
            exit();
        }
    } else {
        echo "<script>alert('Invalid credentials. Please try again.'); window.location.href='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FreshFold Laundry Services - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        .left-side {
            flex: 1;
            background: url('FFLSbg.jpg') no-repeat center center/cover;
        }
        .right-side {
            width: 30%;
            background-color: #d1e3ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            text-align: center;
        }
        .logo {
            width: 230px;
            margin-bottom: 20px;
        }
        .input-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
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
        .login {
            background-color: #6a9ff8;
            color: white;
        }
        .signup {
            background-color: white;
            border: 1px solid #6a9ff8;
            color: #6a9ff8;
        }
        .signup:hover {
            background-color: #6a9ff8;
            color: white;
        }
    </style>
</head>
<body>

    <div class="left-side"></div>

    <div class="right-side">
        <img src="FFLSlogo.png" alt="FreshFold Logo" class="logo">
        <form class="input-container" method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>

            <div class="buttons">
                <button type="button" class="signup" onclick="redirectToSignUp()">Sign Up</button>
                <button type="submit" class="login">Login</button>
            </div>
        </form>
    </div>

    <script>
        function redirectToSignUp() {
            window.location.href = "sign_up.php";
        }
    </script>

</body>
</html>
