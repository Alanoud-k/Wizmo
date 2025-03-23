<?php
session_start(); // Start the session

// Database connection
$servername = "localhost";
$username = "root"; // Default username for MAMP
$password = "root"; // Default password for MAMP
$dbname = "Wizmo";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all products from the Product table
$sql = "SELECT * FROM Product";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row; // Add each product to the array
    }
}

echo json_encode($products); // Return products as JSON

$conn->close();
?>