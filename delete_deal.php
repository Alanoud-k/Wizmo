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
    header('Location: index.php');
    exit();
}

$current_username = $_SESSION['username'];

// Check if deal ID is provided
if (!isset($_GET['id'])) {
    header("Location: deals.php");
    exit();
}

$dealID = $_GET['id'];

// Begin a transaction to ensure data consistency
$conn->begin_transaction();

try {
    // First, get the deal details to know which product and quantity to update
    $deal_sql = "SELECT productName, quantity FROM Deal WHERE dealID = ? AND username = ?";
    $deal_stmt = $conn->prepare($deal_sql);
    $deal_stmt->bind_param("is", $dealID, $current_username);
    $deal_stmt->execute();
    $deal_result = $deal_stmt->get_result();
    
    if ($deal_result->num_rows === 0) {
        throw new Exception("Deal not found or doesn't belong to you.");
    }
    
    $deal_data = $deal_result->fetch_assoc();
    $product_name = $deal_data['productName'];
    $deal_quantity = $deal_data['quantity'];
    
    // Get the current product quantity
    $product_sql = "SELECT quantity FROM Product WHERE productName = ? AND username = ?";
    $product_stmt = $conn->prepare($product_sql);
    $product_stmt->bind_param("ss", $product_name, $current_username);
    $product_stmt->execute();
    $product_result = $product_stmt->get_result();
    
    if ($product_result->num_rows === 0) {
        throw new Exception("Product not found or doesn't belong to you.");
    }
    
    $product_data = $product_result->fetch_assoc();
    $current_quantity = $product_data['quantity'];
    
    // Extract numeric values and units
    $current_quantity_value = preg_replace('/[^0-9.]/', '', $current_quantity);
    $deal_quantity_value = preg_replace('/[^0-9.]/', '', $deal_quantity);
    
    // Get the unit of measurement
    preg_match('/[a-zA-Z]+/', $current_quantity, $unit_matches);
    $unit = !empty($unit_matches) ? $unit_matches[0] : '';
    
    // Calculate new quantity (current + deal quantity to be returned)
    $new_quantity_value = floatval($current_quantity_value) + floatval($deal_quantity_value);
    $new_quantity = $new_quantity_value . $unit;
    
    // Update the product quantity
    $update_sql = "UPDATE Product SET quantity = ? WHERE productName = ? AND username = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sss", $new_quantity, $product_name, $current_username);
    
    if (!$update_stmt->execute()) {
        throw new Exception("Failed to update product quantity.");
    }
    
    // Delete the deal
    $delete_sql = "DELETE FROM Deal WHERE dealID = ? AND username = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("is", $dealID, $current_username);
    
    if (!$delete_stmt->execute()) {
        throw new Exception("Failed to delete deal.");
    }
    
    // Commit the transaction
    $conn->commit();
    
    // Redirect with success message
    header("Location: deals.php?deleted=1");
    exit();
    
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    
    // Redirect with error message
    header("Location: deals.php?error=" . urlencode($e->getMessage()));
    exit();
}


?>