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

// Handle form submission for updating a deal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editDeal'])) {
    $distributorName = $_POST['distributorName'];
    $productName = $_POST['productName'];
    $quantity = $_POST['quantity'];
    $date = $_POST['date'];
    
    // Begin transaction to ensure data consistency
    $conn->begin_transaction();
    
    try {
        // Get the original deal details
        $original_sql = "SELECT productName, quantity FROM Deal WHERE dealID = ? AND username = ?";
        $original_stmt = $conn->prepare($original_sql);
        $original_stmt->bind_param("is", $dealID, $current_username);
        $original_stmt->execute();
        $original_result = $original_stmt->get_result();
        
        if ($original_result->num_rows === 0) {
            throw new Exception("Deal not found or doesn't belong to you.");
        }
        
        $original_deal = $original_result->fetch_assoc();
        $original_product_name = $original_deal['productName'];
        $original_quantity = $original_deal['quantity'];
        
        // Check if the product has changed
        $product_changed = ($original_product_name != $productName);
        
        // Extract numeric values from quantities
        $original_quantity_value = preg_replace('/[^0-9.]/', '', $original_quantity);
        $new_quantity_value = preg_replace('/[^0-9.]/', '', $quantity);
        
        // If product didn't change, just handle the quantity difference
        if (!$product_changed) {
            // Get the product's current quantity
            $product_sql = "SELECT quantity FROM Product WHERE productName = ? AND username = ?";
            $product_stmt = $conn->prepare($product_sql);
            $product_stmt->bind_param("ss", $productName, $current_username);
            $product_stmt->execute();
            $product_result = $product_stmt->get_result();
            
            if ($product_result->num_rows === 0) {
                throw new Exception("Product not found or doesn't belong to you.");
            }
            
            $product_data = $product_result->fetch_assoc();
            $current_product_quantity = $product_data['quantity'];
            
            // Extract numeric value and unit
            $current_product_quantity_value = preg_replace('/[^0-9.]/', '', $current_product_quantity);
            preg_match('/[a-zA-Z]+/', $current_product_quantity, $unit_matches);
            $unit = !empty($unit_matches) ? $unit_matches[0] : '';
            
            // Calculate the difference
            $quantity_difference = floatval($new_quantity_value) - floatval($original_quantity_value);
            
            // If new quantity is higher, check if enough product is available
            if ($quantity_difference > 0) {
                if (floatval($current_product_quantity_value) < $quantity_difference) {
                    throw new Exception("Not enough quantity available for the new deal amount.");
                }
                // Decrease product quantity
                $new_product_quantity_value = floatval($current_product_quantity_value) - $quantity_difference;
            } else {
                // Increase product quantity (return stock)
                $new_product_quantity_value = floatval($current_product_quantity_value) - $quantity_difference;
            }
            
            $new_product_quantity = $new_product_quantity_value . $unit;
            
            // Update the product quantity
            $update_product_sql = "UPDATE Product SET quantity = ? WHERE productName = ? AND username = ?";
            $update_product_stmt = $conn->prepare($update_product_sql);
            $update_product_stmt->bind_param("sss", $new_product_quantity, $productName, $current_username);
            $update_product_stmt->execute();
        } else {
            // Handle product change - return quantity to original product and deduct from new product
            
            // Return quantity to original product
            $original_product_sql = "SELECT quantity FROM Product WHERE productName = ? AND username = ?";
            $original_product_stmt = $conn->prepare($original_product_sql);
            $original_product_stmt->bind_param("ss", $original_product_name, $current_username);
            $original_product_stmt->execute();
            $original_product_result = $original_product_stmt->get_result();
            
            if ($original_product_result->num_rows === 0) {
                throw new Exception("Original product not found or doesn't belong to you.");
            }
            
            $original_product_data = $original_product_result->fetch_assoc();
            $original_product_quantity = $original_product_data['quantity'];
            
            // Extract numeric value and unit
            $original_product_quantity_value = preg_replace('/[^0-9.]/', '', $original_product_quantity);
            preg_match('/[a-zA-Z]+/', $original_product_quantity, $original_unit_matches);
            $original_unit = !empty($original_unit_matches) ? $original_unit_matches[0] : '';
            
            // Calculate new quantity for original product
            $new_original_product_quantity_value = floatval($original_product_quantity_value) + floatval($original_quantity_value);
            $new_original_product_quantity = $new_original_product_quantity_value . $original_unit;
            
            // Update the original product quantity
            $update_original_product_sql = "UPDATE Product SET quantity = ? WHERE productName = ? AND username = ?";
            $update_original_product_stmt = $conn->prepare($update_original_product_sql);
            $update_original_product_stmt->bind_param("sss", $new_original_product_quantity, $original_product_name, $current_username);
            $update_original_product_stmt->execute();
            
            // Deduct quantity from new product
            $new_product_sql = "SELECT quantity FROM Product WHERE productName = ? AND username = ?";
            $new_product_stmt = $conn->prepare($new_product_sql);
            $new_product_stmt->bind_param("ss", $productName, $current_username);
            $new_product_stmt->execute();
            $new_product_result = $new_product_stmt->get_result();
            
            if ($new_product_result->num_rows === 0) {
                throw new Exception("New product not found or doesn't belong to you.");
            }
            
            $new_product_data = $new_product_result->fetch_assoc();
            $new_product_quantity = $new_product_data['quantity'];
            
            // Extract numeric value and unit
            $new_product_quantity_value = preg_replace('/[^0-9.]/', '', $new_product_quantity);
            preg_match('/[a-zA-Z]+/', $new_product_quantity, $new_unit_matches);
            $new_unit = !empty($new_unit_matches) ? $new_unit_matches[0] : '';
            
            // Check if enough quantity is available
            if (floatval($new_product_quantity_value) < floatval($new_quantity_value)) {
                throw new Exception("Not enough quantity available in the new product.");
            }
            
            // Calculate new quantity for new product
            $updated_new_product_quantity_value = floatval($new_product_quantity_value) - floatval($new_quantity_value);
            $updated_new_product_quantity = $updated_new_product_quantity_value . $new_unit;
            
            // Update the new product quantity
            $update_new_product_sql = "UPDATE Product SET quantity = ? WHERE productName = ? AND username = ?";
            $update_new_product_stmt = $conn->prepare($update_new_product_sql);
            $update_new_product_stmt->bind_param("sss", $updated_new_product_quantity, $productName, $current_username);
            $update_new_product_stmt->execute();
        }
        
        // Update the deal
        $sql = "UPDATE Deal SET distributorName = ?, productName = ?, quantity = ?, date = ? 
                WHERE dealID = ? AND username = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssis", $distributorName, $productName, $quantity, $date, $dealID, $current_username);
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating deal: " . $stmt->error);
        }
        
        // Commit the transaction
        $conn->commit();
        
        header("Location: deals.php?updated=1");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch the deal details for editing
$sql = "SELECT * FROM Deal WHERE dealID = ? AND username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $dealID, $current_username);
$stmt->execute();
$result = $stmt->get_result();

// If deal doesn't exist or doesn't belong to current user, redirect
if ($result->num_rows === 0) {
    header("Location: deals.php");
    exit();
}

$deal = $result->fetch_assoc();

// Fetch all products for the current user (for dropdown)
$products_sql = "SELECT productName, quantity FROM Product WHERE username = ?";
$products_stmt = $conn->prepare($products_sql);
$products_stmt->bind_param("s", $current_username);
$products_stmt->execute();
$products_result = $products_stmt->get_result();

$products = array();
while ($product_row = $products_result->fetch_assoc()) {
    $products[] = $product_row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Deal</title>
    <link rel="stylesheet" href="MainStyles.css">
    
    <style>
        body {
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            text-align: center;
        }
        
        .edit-form-container {
            width: 60%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        form input, form textarea, form select {
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        button {
            background-color: #1e3348; 
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            font-size: 17px;
            font-weight: 800;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: inline-block;
            position: relative;
            border-radius: 25px; 
            transition: all 0.3s ease;
            border: none; 
            cursor: pointer;
            z-index: 1; 
        }

        button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 100%; 
            height: 100%; 
            background: linear-gradient(130deg, #800080, #e47a8f);  
            border-radius: 25px; 
            opacity: 0; 
            transition: all 0.3s ease;
            z-index: -1; 
        }

        button:hover::before,
        button.active::before {
            transform: translate(-50%, -50%) scale(1); 
            opacity: 1; 
        }

        button:hover {
            color: white;
            z-index: 1; 
        }
        
        .button-group {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        
        .error-message {
            background-color: #f2dede;
            border-color: #ebccd1;
            color: #a94442;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        
        #quantityHelp {
            font-size: 14px;
            color: #31708f;
            background-color: #d9edf7;
            border: 1px solid #bce8f1;
            padding: 8px;
            border-radius: 5px;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <img src="images/wizmoLOGO.png" alt="logo" width="90" height="80" class="Navimg">
            <div class="text-container">
                <h2>WIZMO</h2>
                <h3>your smartest warehouse gizmo</h3>
            </div>
        </div>
    </header>

    <nav class="navBar">
        <ul>
            <li class="link1"><a href="aboutus.html">About us</a></li>
            <li class="link2"><a href="Products.html">Products warehouse</a></li>
            <li class="link3"><a href="deals.php" class="active">Distributions deals</a></li>
            <li class="link4"><a href="community.html">Community</a></li>
            <li class="link5"><a href="request.html">Requests</a></li>
        </ul>
    </nav>

    <div class="edit-form-container">
        <h2>Edit Deal</h2>
        
        <?php if(isset($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="edit_deal.php?id=<?php echo $dealID; ?>">
            <select id="productName" name="productName" required onchange="updateQuantityInfo()">
                <option value="">Select a product</option>
                <?php foreach($products as $product): ?>
                    <option value="<?php echo htmlspecialchars($product['productName']); ?>"
                            data-quantity="<?php echo htmlspecialchars($product['quantity']); ?>"
                            <?php if($product['productName'] === $deal['productName']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($product['productName']); ?>
                        (Available: <?php echo htmlspecialchars($product['quantity']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" id="distributorName" name="distributorName" 
                   value="<?php echo htmlspecialchars($deal['distributorName']); ?>" 
                   placeholder="Distributor Name" required>
            
            <div>
                <input type="text" id="quantity" name="quantity" 
                       value="<?php echo htmlspecialchars($deal['quantity']); ?>" 
                       placeholder="Quantity" required>
                <div id="quantityHelp"></div>
            </div>
            
            <input type="date" id="date" name="date" 
                   value="<?php echo date('Y-m-d', strtotime($deal['date'])); ?>" required>
            
            <input type="hidden" name="editDeal" value="1">
            
            <div class="button-group">
                <button type="submit">Save Changes</button>
                <a href="deals.php"><button type="button">Cancel</button></a>
            </div>
        </form>
    </div>
    
    <script>
        // Initialize quantity info on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateQuantityInfo();
        });
    
        function updateQuantityInfo() {
            const productSelect = document.getElementById('productName');
            const quantityInput = document.getElementById('quantity');
            const quantityHelp = document.getElementById('quantityHelp');
            const originalProduct = "<?php echo htmlspecialchars($deal['productName']); ?>";
            const originalQuantity = "<?php echo htmlspecialchars($deal['quantity']); ?>";
            
            if (productSelect.selectedIndex > 0) {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const selectedProduct = selectedOption.value;
                const availableQuantity = selectedOption.getAttribute('data-quantity');
                
                // If product hasn't changed, we need to account for the original quantity
                if (selectedProduct === originalProduct) {
                    // Extract numeric parts
                    const numericAvailable = parseFloat(availableQuantity.replace(/[^0-9.]/g, ''));
                    const numericOriginal = parseFloat(originalQuantity.replace(/[^0-9.]/g, ''));
                    const unit = availableQuantity.replace(/[0-9.]/g, '').trim();
                    
                    // Calculate total available (current + what's already in the deal)
                    const totalAvailable = numericAvailable + numericOriginal;
                    const displayTotal = totalAvailable + unit;
                    
                    quantityHelp.textContent = `Available quantity: ${displayTotal} (includes current deal quantity)`;
                    quantityInput.setAttribute('data-max', totalAvailable);
                } else {
                    // Just show the available quantity for the new product
                    quantityHelp.textContent = `Available quantity: ${availableQuantity}`;
                    
                    // Extract only the numeric part for validation
                    const numericAvailable = availableQuantity.replace(/[^0-9.]/g, '');
                    quantityInput.setAttribute('data-max', numericAvailable);
                }
                
                quantityHelp.style.display = 'block';
            } else {
                quantityHelp.style.display = 'none';
            }
        }
        
        // Validate the form before submission
        document.getElementById('quantity').addEventListener('input', function() {
            const quantityInput = document.getElementById('quantity');
            const maxQuantity = parseFloat(quantityInput.getAttribute('data-max') || 0);
            
            // Extract numeric value from input
            const inputValue = quantityInput.value.replace(/[^0-9.]/g, '');
            
            if (parseFloat(inputValue) > maxQuantity) {
                quantityInput.setCustomValidity(`Quantity exceeds available amount. Maximum available: ${maxQuantity}`);
            } else {
                quantityInput.setCustomValidity('');
            }
        });
    </script>
</body>
</html>