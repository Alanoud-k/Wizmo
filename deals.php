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

// Assume user is logged in and username is stored in session
// If not, you should implement proper authentication
if (!isset($_SESSION['username'])) {
    // For testing purposes, you can set a default username
    $_SESSION['username'] = "rahaf5"; // Replace with actual login system
}

$current_username = $_SESSION['username'];
$error_message = "";
$success_message = "";

// Handle form submission for adding a new deal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addDeal'])) {
    $distributorName = $_POST['distributorName'];
    $productName = $_POST['productName'];
    $quantity = $_POST['quantity'];
    $date = $_POST['date'];
    $username = $current_username;

    // Start transaction to ensure data consistency
    $conn->begin_transaction();
    
    try {
        // Check if product exists and has enough quantity
        $check_sql = "SELECT quantity FROM Product WHERE productName = ? AND username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $productName, $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            throw new Exception("Product not found or does not belong to you.");
        }
        
        $product_data = $check_result->fetch_assoc();
        $current_quantity = $product_data['quantity'];
        
        // Extract numeric value from quantity (removing "m", "pcs", etc.)
        $current_quantity_value = preg_replace('/[^0-9.]/', '', $current_quantity);
        $requested_quantity_value = preg_replace('/[^0-9.]/', '', $quantity);
        
        // Get the unit of measurement, if any (e.g., "m", "pcs")
        preg_match('/[a-zA-Z]+/', $current_quantity, $unit_matches);
        $unit = !empty($unit_matches) ? $unit_matches[0] : '';
        
        // Compare quantities
        if (floatval($requested_quantity_value) > floatval($current_quantity_value)) {
            throw new Exception("Not enough quantity available. Available: {$current_quantity}");
        }
        
        // Calculate new quantity
        $new_quantity_value = floatval($current_quantity_value) - floatval($requested_quantity_value);
        $new_quantity = $new_quantity_value . $unit;
        
        // Update product quantity
        $update_sql = "UPDATE Product SET quantity = ? WHERE productName = ? AND username = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sss", $new_quantity, $productName, $username);
        $update_stmt->execute();
        
        // Insert new deal
        $insert_sql = "INSERT INTO Deal (distributorName, productName, username, quantity, date) 
                       VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sssss", $distributorName, $productName, $username, $quantity, $date);
        $insert_stmt->execute();
        
        // Commit transaction
        $conn->commit();
        $success_message = "Deal has been successfully added and product quantity updated!";
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch all deals for the current user
$sql = "SELECT d.*, p.image FROM Deal d 
        LEFT JOIN Product p ON d.productName = p.productName 
        WHERE d.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_username);
$stmt->execute();
$result = $stmt->get_result();

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
    <title>Deals</title>
    <link rel="stylesheet" href="MainStyles.css">

    <svg style="display: none;">
        <symbol id="facebook" viewBox="0 0 24 24">
          <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
        </symbol>
        <symbol id="twitter" viewBox="0 0 24 24">
          <path d="M22.23 5.924a8.212 8.212 0 0 1-2.357.646 4.115 4.115 0 0 0 1.804-2.27 8.221 8.221 0 0 1-2.606.996 4.103 4.103 0 0 0-7.095 2.807 4.1 4.1 0 0 0 .105.936 11.648 11.648 0 0 1-8.457-4.287 4.103 4.103 0 0 0 1.27 5.478 4.09 4.09 0 0 1-1.858-.513v.052a4.104 4.104 0 0 0 3.292 4.023 4.099 4.099 0 0 1-1.853.07 4.105 4.105 0 0 0 3.833 2.85 8.231 8.231 0 0 1-5.096 1.756c-.332 0-.658-.02-.979-.057a11.616 11.616 0 0 0 6.29 1.843c7.547 0 11.675-6.252 11.675-11.675 0-.178-.004-.355-.012-.531a8.348 8.348 0 0 0 2.047-2.123z"/>
        </symbol>
        <symbol id="instagram" viewBox="0 0 24 24">
          <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a3.162 3.162 0 1 0 0 6.324 3.162 3.162 0 0 0 0-6.324zm0 5.162a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5.146-7.85a1.237 1.237 0 1 0 0 2.474 1.237 1.237 0 0 0 0-2.474z"/>
        </symbol>
        <symbol id="linkedin" viewBox="0 0 24 24">
          <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.784 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
        </symbol>
    </svg>
    <svg style="display: none;">
        <symbol id="home" viewBox="0 0 24 24">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
        </symbol>
        <symbol id="account" viewBox="0 0 24 24">
            <path d="M12 2a5 5 0 1 0 5 5 5 5 0 0 0-5-5zm0 8a3 3 0 1 1 3-3 3 3 0 0 1-3 3zm9 11v-1a7 7 0 0 0-7-7h-4a7 7 0 0 0-7 7v1h2v-1a5 5 0 0 1 5-5h4a5 5 0 0 1 5 5v1z"/>
        </symbol>
        <symbol id="logout" viewBox="0 0 24 24">
            <path d="M16 17v-3h-5v-2h5V7l5 5-5 5zM14 2a2 2 0 0 1 2 2v2h-2V4H5v16h9v-2h2v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9z"/>
        </symbol>
    </svg>

    <style>
        body {
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            text-align: center;
        }
        #dealsList {
            list-style: none;
            padding: 0;
        }
        .deal-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px;
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

        .popup {
            display: none;  
            position: fixed; 
            z-index: 1;  
            left: 0;
            top: 0;
            width: 100%;  
            height: 100%;  
            overflow: auto;  
            background-color: rgba(0, 0, 0, 0.4);  
        }

        .popup-content {
            background-color: white;
            margin: 15% auto;  
            padding: 20px;
            border-radius: 10px;
            width: 50%;  
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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

        .deal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .success-message {
            background-color: #dff0d8;
            border-color: #d6e9c6;
            color: #3c763d;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
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
        
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .quantity-info {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
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

            <!-- Link images aligned to the right -->
            <div class="header-links">
                <a href="profile.html"><svg class="header-icon"><use href="#account"/></svg></a>
                <a href="HomePage.php"><svg class="header-icon"><use href="#home"/></svg></a>
                <a href="index.php"><svg class="header-icon"><use href="#logout"/></svg></a>
            </div>
        </div>
    </header>

    <!-- Navigation bar -->
    <nav class="navBar">
        <ul>
            <li class="link1"><a href="aboutus.php">About us</a></li>
            <li class="link2"><a href="Products.php">Products warehouse</a></li>
            <li class="link3"><a href="deals.php" class="active">Distributions deals</a></li>
            <li class="link4"><a href="community.php">Community</a></li>
            <li class="link5"><a href="request.html">Requests</a></li>
        </ul>
    </nav>

    <br>
    <?php if(!empty($success_message)): ?>
        <div class="success-message">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if(!empty($error_message)): ?>
        <div class="error-message">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <button class="add-deal-btn" onclick="showPopup()">Add Deal</button>
    
    <ul id="dealsList">
        <?php while($row = $result->fetch_assoc()): ?>
            <li class="deal-item">
                <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'images/placeholder.png'; ?>" 
                     alt="Product" class="product-img">
                <span><?php echo htmlspecialchars($row['productName']); ?> - 
                      <?php echo htmlspecialchars($row['quantity']); ?> - 
                      <?php echo htmlspecialchars($row['date']); ?> - 
                      <?php echo htmlspecialchars($row['distributorName']); ?></span>
                <div class="deal-buttons">
                    <a href="edit_deal.php?id=<?php echo $row['dealID']; ?>"><button>Edit</button></a>
                    <a href="delete_deal.php?id=<?php echo $row['dealID']; ?>" 
                       onclick="return confirm('Are you sure you want to delete this deal?');">
                       <button>Delete</button>
                    </a>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
    
    <div id="popupForm" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <h2 id="popupTitle">Add New Deal</h2>

            <form id="addDealForm" method="POST" action="deals.php"> 
                <select id="productName" name="productName" required onchange="updateQuantityInfo()">
                    <option value="">Select a product</option>
                    <?php foreach($products as $product): ?>
                        <option value="<?php echo htmlspecialchars($product['productName']); ?>" 
                                data-quantity="<?php echo htmlspecialchars($product['quantity']); ?>">
                            <?php echo htmlspecialchars($product['productName']); ?> 
                            (Available: <?php echo htmlspecialchars($product['quantity']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="text" id="distributorName" name="distributorName" placeholder="Distributor Name" required>
                
                <div>
                    <input type="text" id="quantity" name="quantity" placeholder="Quantity" required>
                    <div id="quantityHelp"></div>
                </div>
                
                <input type="date" id="date" name="date" required>
                <!-- Hidden input to identify form submission -->
                <input type="hidden" name="addDeal" value="1">
                <button type="submit" id="submitButton">Add Deal</button>
                <button type="button" onclick="closePopup()">Cancel</button>
            </form>
        </div>
    </div>
    
    <script>
        function showPopup() {
            document.getElementById('popupForm').style.display = 'block';
        }
    
        function closePopup() {
            document.getElementById('popupForm').style.display = 'none';
            document.getElementById('addDealForm').reset();
            document.getElementById('quantityHelp').style.display = 'none';
        }

        function updateQuantityInfo() {
            const productSelect = document.getElementById('productName');
            const quantityInput = document.getElementById('quantity');
            const quantityHelp = document.getElementById('quantityHelp');
            
            if (productSelect.selectedIndex > 0) {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const availableQuantity = selectedOption.getAttribute('data-quantity');
                
                quantityHelp.textContent = `Available quantity: ${availableQuantity}`;
                quantityHelp.style.display = 'block';
                
                // Extract only the numeric part of quantity for validation
                const numericQuantity = availableQuantity.replace(/[^0-9.]/g, '');
                const unit = availableQuantity.replace(/[0-9.]/g, '').trim();
                
                // Set a placeholder showing the expected format
                quantityInput.placeholder = `Quantity (max: ${availableQuantity})`;
                
                // Add a data attribute for validation
                quantityInput.setAttribute('data-max', numericQuantity);
                quantityInput.setAttribute('data-unit', unit);
            } else {
                quantityHelp.style.display = 'none';
                quantityInput.placeholder = 'Quantity';
            }
        }

        // Validate the form before submission
        document.getElementById('addDealForm').addEventListener('submit', function(event) {
            const quantityInput = document.getElementById('quantity');
            const maxQuantity = parseFloat(quantityInput.getAttribute('data-max'));
            
            // Extract numeric value from input
            const inputValue = quantityInput.value.replace(/[^0-9.]/g, '');
            
            if (parseFloat(inputValue) > maxQuantity) {
                event.preventDefault();
                alert(`Quantity exceeds available amount. Maximum available: ${maxQuantity}`);
            }
        });

        // Close the popup when clicking outside of it
        window.onclick = function(event) {
            let popup = document.getElementById('popupForm');
            if (event.target === popup) {
                closePopup();
            }
        }
    </script>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="HomePage.php">Home</a></li>
                    <li><a href="aboutus.php">About Us</a></li>
                    <li><a href="Products.php">Products</a></li>
                    <li><a href="deals.php">Deals</a></li>
                    <li><a href="community.php">Community</a></li>
                    <li><a href="request.html">Requests</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact Us</h4>
                <p>Email: support@wizmo.com</p>
                <p>Phone: +966 (123) 456-7890</p>
                <p>Address: 123 KSU, Riyadh, SA</p>
            </div>
            <div class="footer-section">
                <h4>Follow Us</h4>
                <a href="https://www.facebook.com"><svg width="24" height="24"><use href="#facebook"/></svg></a>
                <a href="https://twitter.com"><svg width="24" height="24"><use href="#twitter"/></svg></a>
                <a href="https://www.instagram.com"><svg width="24" height="24"><use href="#instagram"/></svg></a>
                <a href="https://www.linkedin.com"><svg width="24" height="24"><use href="#linkedin"/></svg></a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 WIZMO. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
