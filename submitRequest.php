<?php
// Prevent any HTML output before our response
header('Content-Type: text/plain');

session_start();
if (!isset($_SESSION['username'])) {
    echo "error: not logged in";
    exit();
}

$connection = new mysqli("localhost", "root", "root", "Wizmo");
if ($connection->connect_error) {
    echo "error: database connection failed";
    exit();
}

// Check if POST variables exist
if (!isset($_POST['senderUsername']) || !isset($_POST['receiverUsername']) || !isset($_POST['message'])) {
    echo "error: missing form data";
    exit();
}

$senderUsername = $_POST['senderUsername'];
$receiverUsername = $_POST['receiverUsername'];
$message = $_POST['message'];

// Validate input
if (empty($senderUsername) || empty($receiverUsername) || empty($message)) {
    echo "error: missing required fields";
    exit();
}

// Sanitize input to prevent SQL injection
$senderUsername = $connection->real_escape_string($senderUsername);
$receiverUsername = $connection->real_escape_string($receiverUsername);
$message = $connection->real_escape_string($message);

// Insert the request with 'Pending' state
$sql = "INSERT INTO Request (state, message, username, receiverUsername) VALUES ('Pending', '$message', '$senderUsername', '$receiverUsername')";

if ($connection->query($sql)) {
    echo "success";
} else {
    echo "error: " . $connection->error;
}

$connection->close();
?>