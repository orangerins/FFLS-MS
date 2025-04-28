<?php
session_start();
include 'db_connection.php';

// Ensure client is logged in
if (!isset($_SESSION['client_logged_in'], $_SESSION['client_username'])) {
    echo "<script>alert('Please log in to update your profile.'); window.location.href='login.php';</script>";
    exit();
}

// Fetch the user's ID from the account table using session username
$clientUsername = mysqli_real_escape_string($conn, $_SESSION['client_username']);
$sqlUID = "SELECT user_id FROM user_account WHERE username='$clientUsername'";
$resUID = $conn->query($sqlUID);
if (!$resUID || $resUID->num_rows === 0) {
    echo "<script>alert('User not found. Please log in again.'); window.location.href='login.php';</script>";
    exit();
}
$rowUID = $resUID->fetch_assoc();
$user_id = $rowUID['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize POST data
    $fullName    = mysqli_real_escape_string($conn, $_POST['name']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $newUsername = mysqli_real_escape_string($conn, $_POST['username']);
    $address     = mysqli_real_escape_string($conn, $_POST['address']);

    // Split full name into first and last
    $parts     = explode(' ', trim($fullName), 2);
    $first_name = $parts[0];
    $last_name  = isset($parts[1]) ? $parts[1] : '';

    // Update users table
    $updateUser = "UPDATE users SET
        first_name='$first_name',
        last_name='$last_name',
        email='$email',
        street='$address'
        WHERE user_id='$user_id'";

    if ($conn->query($updateUser)) {
        // If username changed, update account table
        if ($newUsername !== $_SESSION['client_username']) {
            $updateAccount = "UPDATE user_account SET username='$newUsername' WHERE user_id='$user_id'";
            $conn->query($updateAccount);
        }
        // Logout after profile update
        session_unset();
        session_destroy();
        echo "<script>
                alert('Profile updated. Please log in again.');
                window.location.href='login.php';
              </script>";
        exit();
    } else {
        echo "<script>
                alert('Error updating profile: " . addslashes($conn->error) . "');
                window.location.href='Dashboard-Client.php';
              </script>";
        exit();
    }
}
?>
