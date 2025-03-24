<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    die(json_encode(["error" => "User not logged in."]));
}

// Database connection
$servername = "localhost";
$username = "root"; // Default username for MAMP
$password = "root"; // Default password for MAMP
$dbname = "Wizmo";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Fetch products for the logged-in user
$loggedInUser = $_SESSION['username'];
$sql = "SELECT * FROM Product WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInUser);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row; // Add each product to the array
    }
}

// Set the response header to JSON
header('Content-Type: application/json');

// Return products as JSON
echo json_encode($products);

$stmt->close();
$conn->close();
?>