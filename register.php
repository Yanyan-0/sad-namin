<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "hardware_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $conn->real_escape_string($_POST['fname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $address = $conn->real_escape_string($_POST['address']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    // Default role for new users
    $role = 'customer';

    // Check if username already exists
    $check = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Username already exists.'); window.location='index.php';</script>";
        exit();
    }

    // Insert new user
    $sql = "INSERT INTO users (fname, lname, address, contact, email, username, password, role) 
            VALUES ('$fname', '$lname', '$address', '$contact', '$email', '$username', '$password', '$role')";

    if ($conn->query($sql) === TRUE) {
        // Auto-login after successful registration
        $user_id = $conn->insert_id;
        $_SESSION['id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        // Redirect to homepage
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Registration failed. Please try again.'); window.location='index.php';</script>";
    }
}
?>
