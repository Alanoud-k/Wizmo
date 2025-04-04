<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "root"; // Assuming MAMP default password
$dbname = "Wizmo";
$port = 8889; // MAMP default port

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle product operations
$message = "";

// Add new product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "add") {
    $productName = $_POST["productName"];
    $quantity = $_POST["quantity"];
    $description = $_POST["description"];
    $price = $_POST["price"] . "SAR";
    $username = $_POST["username"]; // Currently logged in user

    // Handle image upload
    $targetDir = "images/";
    $fileName = basename($_FILES["productImage"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Check if product name already exists
    $checkSql = "SELECT * FROM Product WHERE productName = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $productName);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        $message = "Product name already exists!";
    } else {
        // Upload file
        if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFilePath)) {
            // Insert product data into database
            $sql = "INSERT INTO Product (productName, quantity, description, image, price, username) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $productName, $quantity, $description, $targetFilePath, $price, $username);
            
            if ($stmt->execute()) {
                $message = "Product added successfully!";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error uploading image.";
        }
    }
    $checkStmt->close();
}

// Delete product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "delete") {
    $productName = $_POST["productName"];
    
    // Check if product is referenced in Deal table
    $checkSql = "SELECT * FROM Deal WHERE productName = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $productName);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        $message = "Cannot delete product because it has associated deals!";
    } else {
        // Delete the product
        $sql = "DELETE FROM Product WHERE productName = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $productName);
        
        if ($stmt->execute()) {
            $message = "Product deleted successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $checkStmt->close();
}

// Update product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "update") {
    $oldProductName = $_POST["oldProductName"];
    $productName = $_POST["productName"];
    $quantity = $_POST["quantity"];
    $description = $_POST["description"];
    $price = $_POST["price"] . "SAR";
    
    // Check if updating image
    if ($_FILES["productImage"]["size"] > 0) {
        // Handle image upload
        $targetDir = "images/";
        $fileName = basename($_FILES["productImage"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        
        if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFilePath)) {
            // Update product with new image
            $sql = "UPDATE Product SET productName = ?, quantity = ?, description = ?, image = ?, price = ? 
                    WHERE productName = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $productName, $quantity, $description, $targetFilePath, $price, $oldProductName);
        } else {
            $message = "Error uploading image.";
        }
    } else {
        // Update product without changing image
        $sql = "UPDATE Product SET productName = ?, quantity = ?, description = ?, price = ? 
                WHERE productName = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $productName, $quantity, $description, $price, $oldProductName);
    }
    
    // Execute update
    if (isset($stmt)) {
        if ($stmt->execute()) {
            // If product name changed, update Deal table references
            if ($oldProductName != $productName) {
                $updateDealSql = "UPDATE Deal SET productName = ? WHERE productName = ?";
                $updateDealStmt = $conn->prepare($updateDealSql);
                $updateDealStmt->bind_param("ss", $productName, $oldProductName);
                $updateDealStmt->execute();
                $updateDealStmt->close();
            }
            $message = "Product updated successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch products from database
// If user is logged in, fetch only their products
// For this example, let's assume user is logged in and username is stored in session
session_start();
$loggedInUser = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// For demo purposes, you can comment out this condition or set a demo username
if (!$loggedInUser) {
    $loggedInUser = "rahaf5"; // Demo user - remove in production
}

$products = [];
if ($loggedInUser) {
    $sql = "SELECT * FROM Product WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loggedInUser);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Product</title>
    <link rel="stylesheet" href="MainStyles.css">

    <svg style="display: none;">
        <symbol id="facebook" viewBox="0 0 24 24">
            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
        </symbol>
        <symbol id="twitter" viewBox="0 0 24 24">
            <path
                d="M22.23 5.924a8.212 8.212 0 0 1-2.357.646 4.115 4.115 0 0 0 1.804-2.27 8.221 8.221 0 0 1-2.606.996 4.103 4.103 0 0 0-7.095 2.807 4.1 4.1 0 0 0 .105.936 11.648 11.648 0 0 1-8.457-4.287 4.103 4.103 0 0 0 1.27 5.478 4.09 4.09 0 0 1-1.858-.513v.052a4.104 4.104 0 0 0 3.292 4.023 4.099 4.099 0 0 1-1.853.07 4.105 4.105 0 0 0 3.833 2.85 8.231 8.231 0 0 1-5.096 1.756c-.332 0-.658-.02-.979-.057a11.616 11.616 0 0 0 6.29 1.843c7.547 0 11.675-6.252 11.675-11.675 0-.178-.004-.355-.012-.531a8.348 8.348 0 0 0 2.047-2.123z" />
        </symbol>
        <symbol id="instagram" viewBox="0 0 24 24">
            <path
                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a3.162 3.162 0 1 0 0 6.324 3.162 3.162 0 0 0 0-6.324zm0 5.162a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5.146-7.85a1.237 1.237 0 1 0 0 2.474 1.237 1.237 0 0 0 0-2.474z" />
        </symbol>
        <symbol id="linkedin" viewBox="0 0 24 24">
            <path
                d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.784 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
        </symbol>
    </svg>
    <svg style="display: none;">
        <symbol id="home" viewBox="0 0 24 24">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
        </symbol>
        <symbol id="account" viewBox="0 0 24 24">
            <path
                d="M12 2a5 5 0 1 0 5 5 5 5 0 0 0-5-5zm0 8a3 3 0 1 1 3-3 3 3 0 0 1-3 3zm9 11v-1a7 7 0 0 0-7-7h-4a7 7 0 0 0-7 7v1h2v-1a5 5 0 0 1 5-5h4a5 5 0 0 1 5 5v1z" />
        </symbol>
        <symbol id="logout" viewBox="0 0 24 24">
            <path
                d="M16 17v-3h-5v-2h5V7l5 5-5 5zM14 2a2 2 0 0 1 2 2v2h-2V4H5v16h9v-2h2v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9z" />
        </symbol>
        <symbol id="search" viewBox="0 0 24 24">
            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
        </symbol>
    </svg>

    <style>
        /* Base styles */
        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        /* Modern product card design */
        .product-card {
            position: relative;
            width: 280px;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.4s ease;
            margin: 10px;
            border: 1px solid #eaeaea;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            background-color: white;
            display: flex;
            flex-direction: column;
        }

        .product-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-info {
            padding: 15px;
            background-color: white;
            color: #333;
            text-align: center;
            font-weight: 500;
            transition: all 0.3s ease;
            transform: translateY(0);
            opacity: 1;
            font-size: 16px;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }

        .product-card:hover img {
            transform: scale(1.05);
        }

        /* Hover effect for product info */
        .product-card .product-hover-info {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    background: linear-gradient(to top, rgba(165, 42, 42, 0.9), rgba(210, 105, 30, 0.8));
    padding: 20px 15px;
    color: white;
    transform: translateY(100%);
    transition: transform 0.4s ease;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

        .product-card:hover .product-hover-info {
            transform: translateY(0);
        }

        /* Low stock indication */
        .low-stock {
            position: relative;
        }

        .low-stock::before {
            content: 'Low Stock';
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: rgba(255, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            z-index: 2;
        }

        .low-stock .product-info {
            border-top: 3px solid #ff0000;
        }

        /* Delete and Edit buttons */
        .product-card .buttons {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
            display: flex;
            gap: 8px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover .buttons {
            opacity: 1;
        }

        .product-card .buttons button {
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .product-card .buttons .edit-btn:hover {
            background-color: #1a73e8;
        }

        .product-card .buttons .delete-btn:hover {
            background-color: #d93025;
        }

        /* Custom button styling for popup */
        .add-product-btn {
            background-color: #1e3348;
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            font-size: 17px;
            font-weight: 800;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            margin: 20px;
            display: inline-block;
            position: relative;
            border-radius: 30px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            z-index: 1;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .add-product-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 100%;
            height: 100%;
            background: linear-gradient(130deg, #800080, #e47a8f);
            border-radius: 30px;
            opacity: 0;
            transition: all 0.4s ease;
            z-index: -1;
        }

        .add-product-btn:hover::before,
        .add-product-btn.active::before {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }

        .add-product-btn:hover {
            color: white;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        /* Popup styling */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .popup h2 {
            margin-top: 0;
            color: #1e3348;
            text-align: center;
            margin-bottom: 20px;
        }

        .popup form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .popup input, .popup button, .popup textarea {
            padding: 12px;
            border-radius: 8px;
        }

        .popup input, .popup textarea {
            border: 1px solid #ddd;
        }

        .popup input:focus, .popup textarea:focus {
            outline: none;
            border-color: #1e3348;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        /* Custom file input */
        .image-preview-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .image-preview {
            width: 100%;
            height: 150px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .image-placeholder {
            color: #999;
            text-align: center;
        }

        /* Form description */
        .form-description {
            margin-bottom: 15px;
            color: #666;
            line-height: 1.5;
            text-align: center;
            font-size: 14px;
        }

        /* Search bar */
        .search-container {
            display: flex;
            justify-content: center;
            margin: 20px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .search-bar {
            display: flex;
            width: 100%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 30px;
            overflow: hidden;
        }

        .search-bar input {
            flex-grow: 1;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            outline: none;
        }

        .search-bar button {
            background-color: #1e3348;
            color: white;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-bar button:hover {
            background: linear-gradient(130deg, #800080, #e47a8f);
        }

        .search-bar button svg {
            width: 20px;
            height: 20px;
            fill: white;
        }

        /* Message alert */
        .alert-message {
            padding: 15px;
            margin: 20px;
            border-radius: 8px;
            text-align: center;
            animation: fadeOut 5s forwards;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; visibility: hidden; }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .product-card {
                width: calc(50% - 20px);
            }
            
            .search-container {
                margin: 15px;
            }
        }

        @media (max-width: 480px) {
            .product-card {
                width: 100%;
            }
            
            .search-bar {
                flex-direction: column;
                border-radius: 15px;
            }
            
            .search-bar input {
                width: 100%;
                border-radius: 15px 15px 0 0;
            }
            
            .search-bar button {
                width: 100%;
                border-radius: 0 0 15px 15px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="header-container">
            <img src="wizmoLOGO.png" alt="logo" width="90" height="80" class="Navimg">
            <div class="text-container">
                <h2>WIZMO</h2>
                <h3>your smartest warehouse gizmo</h3>
            </div>

            <!-- Link images aligned to the right -->
            <div class="header-links">
                <a href="profile.php"><svg class="header-icon">
                        <use href="#account" />
                    </svg></a>
                <a href="HomePage.php"><svg class="header-icon">
                        <use href="#home" />
                    </svg></a>
                <a href="signup.php"><svg class="header-icon">
                        <use href="#logout" />
                    </svg></a>
            </div>
        </div>
    </header>

    <!-- Navigation bar -->
    <nav class="navBar">
        <ul>
            <li class="link1"><a href="aboutus.php">About us</a></li>
            <li class="link2"><a href="Products.php" class="active">Products warehouse</a></li>
            <li class="link3"><a href="deals.php">Distributions deals</a></li>
            <li class="link4"><a href="community.php">Community</a></li>
            <li class="link5"><a href="request.php">Request</a></li>
        </ul>
    </nav>

    <br>
    
   <!-- Display message if any -->
<?php if (!empty($message)): ?>
    <div class="alert-message <?php echo (strpos($message, 'successfully') !== false) ? 'alert-success' : 'alert-error'; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<!-- Search bar -->
<div class="search-container">
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search products by name...">
        <button type="button" onclick="searchProducts()">
            <svg>
                <use href="#search" />
            </svg>
        </button>
    </div>
</div>

<button class="add-product-btn" onclick="showAddPopup()">Add Product</button>
<div id="productList" class="product-container">
    <?php 
    // Display products from database
    foreach ($products as $product) {
        $lowStock = false;
        // Check if quantity is less than 10
        // Parse quantity from formats like "5 pcs", "8m", etc.
        $quantityNum = intval($product['quantity']);
        if ($quantityNum < 10) {
            $lowStock = true;
        }
        
        ?>
        <div class="product-card <?php echo $lowStock ? 'low-stock' : ''; ?>">
            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['productName']; ?>">
            <div class="product-info">
                <p><strong><?php echo $product['productName']; ?></strong></p>
                <p><?php echo $product['quantity']; ?></p>
                <p><?php echo $product['price']; ?></p>
            </div>
            <div class="product-hover-info">
                <p><?php echo $product['description']; ?></p>
            </div>
            <div class="buttons">
                <button class="edit-btn" onclick="editProduct('<?php echo $product['productName']; ?>', '<?php echo $product['quantity']; ?>', '<?php echo str_replace('SAR', '', $product['price']); ?>', '<?php echo htmlspecialchars($product['description']); ?>', '<?php echo $product['image']; ?>')">Edit</button>
                <button class="delete-btn" onclick="confirmDelete('<?php echo $product['productName']; ?>')">Delete</button>
            </div>
        </div>
    <?php } ?>
</div>

<div id="popupForm" class="popup">
    <div class="popup-content">
        <h2 id="popupTitle">Add New Product</h2>
        <div class="form-description">
            Fill out the form below to add a new product to your inventory. Upload a clear image of the product, provide a name, specify the quantity in stock, and set the price. Products with less than 10 items will be marked as "Low Stock".
        </div>
        <form id="productForm" enctype="multipart/form-data" method="POST" action="">
            <input type="hidden" id="action" name="action" value="add">
            <input type="hidden" id="oldProductName" name="oldProductName" value="">
            <input type="hidden" id="username" name="username" value="<?php echo $loggedInUser; ?>">
            
            <div class="image-preview-container">
                <div class="image-preview" id="imagePreview">
                    <div class="image-placeholder">Click to select image</div>
                </div>
                <input type="file" id="productImage" name="productImage" style="display: none" accept="image/*" onchange="previewImage(event)">
                <button type="button" onclick="document.getElementById('productImage').click()">Select Image</button>
            </div>
            <input type="text" id="productName" name="productName" placeholder="Product Name" required>
            <input type="text" id="quantity" name="quantity" placeholder="Quantity" required>
            <input type="number" id="price" name="price" placeholder="Price" step="0.01" required>
            <textarea id="description" name="description" placeholder="Product Description" rows="3"></textarea>
            <div style="display: flex; gap: 10px;">
                <button type="submit" id="submitButton">Done</button>
                <button type="button" onclick="closePopup()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete confirmation form -->
<form id="deleteForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" id="deleteProductName" name="productName" value="">
</form>

<script>
    // Function to preview the selected image
    function previewImage(event) {
        let imagePreview = document.getElementById('imagePreview');
        let file = event.target.files[0];
        
        if (file) {
            let reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.innerHTML = '<div class="image-placeholder">Click to select image</div>';
        }
    }

    // Function to show add product popup
    function showAddPopup() {
        document.getElementById('popupTitle').textContent = 'Add New Product';
        document.getElementById('action').value = 'add';
        document.getElementById('oldProductName').value = '';
        document.getElementById('productForm').reset();
        
        // Reset image preview
        let imagePreview = document.getElementById('imagePreview');
        imagePreview.innerHTML = '<div class="image-placeholder">Click to select image</div>';
        
        // Reset submit button text
        document.getElementById('submitButton').textContent = 'Add Product';
        
        // Show popup
        document.getElementById('popupForm').style.display = 'flex';
    }

    // Function to close popup
    function closePopup() {
        document.getElementById('popupForm').style.display = 'none';
    }

    // Function to confirm product deletion
    function confirmDelete(productName) {
        if (confirm(`Are you sure you want to delete the product "${productName}"?`)) {
            document.getElementById('deleteProductName').value = productName;
            document.getElementById('deleteForm').submit();
        }
    }

    // Function to edit product
    function editProduct(name, quantity, price, description, imagePath) {
        document.getElementById('popupTitle').textContent = 'Edit Product';
        document.getElementById('action').value = 'update';
        document.getElementById('oldProductName').value = name;
        document.getElementById('productName').value = name;
        document.getElementById('quantity').value = quantity;
        document.getElementById('price').value = price.replace('SAR', '').trim();
        document.getElementById('description').value = description;
        
        // Update image preview
        let imagePreview = document.getElementById('imagePreview');
        imagePreview.innerHTML = `<img src="${imagePath}" alt="${name}">`;
        
        // Update submit button text
        document.getElementById('submitButton').textContent = 'Update Product';
        
        // Show popup
        document.getElementById('popupForm').style.display = 'flex';
    }

    // Function to search products by name
    function searchProducts() {
        let searchTerm = document.getElementById('searchInput').value.toLowerCase();
        let productCards = document.querySelectorAll('.product-card');
        
        productCards.forEach(card => {
            let productName = card.querySelector('.product-info strong').textContent.toLowerCase();
            
            if (productName.includes(searchTerm)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Add event listener for real-time search as user types
    document.getElementById('searchInput').addEventListener('keyup', function(event) {
        // Search on Enter key press
        if (event.key === 'Enter') {
            searchProducts();
        }
        // Optional: Enable this for real-time search as user types
        // searchProducts();
    });

    // Close popup when clicking outside of it
    window.onclick = function(event) {
        let popup = document.getElementById('popupForm');
        if (event.target === popup) {
            closePopup();
        }
    };
</script>

<!-- Footer -->
<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="HomePage.html">Home</a></li>
                <li><a href="aboutus.html">About Us</a></li>
                <li><a href="Products.php">Products</a></li>
                <li><a href="deals.html">Deals</a></li>
                <li><a href="community.html">Community</a></li>
                <li><a href="request.html">Request</a></li>
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
            <a href="#"><svg width="24" height="24">
                    <use href="#facebook" />
                </svg></a>
            <a href="#"><svg width="24" height="24">
                    <use href="#twitter" />
                </svg></a>
            <a href="#"><svg width="24" height="24">
                    <use href="#instagram" />
                </svg></a>
            <a href="#"><svg width="24" height="24">
                    <use href="#linkedin" />
                </svg></a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 WIZMO. All rights reserved.</p>
    </div>
</footer>
</body>

</html>
