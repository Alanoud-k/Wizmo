<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    die(json_encode(["error" => "User not logged in."]));
}

// Return the username as JSON
echo json_encode(["username" => $_SESSION['username']]);
?>