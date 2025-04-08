<?php
session_start();
$servername = "localhost";
$username = "root"; 
$password = "root";
$dbname = "Wizmo";
$port = 8889;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page or set default for testing
    $_SESSION['username'] = "rahaf5"; // Replace with actual login system
}

$current_username = $_SESSION['username'];

// Check if deal ID is provided
if (!isset($_GET['id'])) {
    header("Location: deals.php");
    exit();
}

$dealID = $_GET['id'];

// Delete the deal if it belongs to the current user
$sql = "DELETE FROM Deal WHERE dealID = ? AND username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $dealID, $current_username);

if ($stmt->execute()) {
    // Redirect with success message
    header("Location: deals.php?deleted=1");
} else {
    // Redirect with error message
    header("Location: deals.php?error=delete-failed");
}

$stmt->close();
$conn->close();
?>