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

// Handle form submission for updating a deal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editDeal'])) {
    $distributorName = $_POST['distributorName'];
    $productName = $_POST['productName'];
    $quantity = $_POST['quantity'];
    $date = $_POST['date'];
    
    $sql = "UPDATE Deal SET distributorName = ?, productName = ?, quantity = ?, date = ? 
            WHERE dealID = ? AND username = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssis", $distributorName, $productName, $quantity, $date, $dealID, $current_username);
    
    if ($stmt->execute()) {
        header("Location: deals.php?updated=1");
        exit();
    } else {
        $error_message = "Error updating deal: " . $stmt->error;
    }
    $stmt->close();
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
$products_sql = "SELECT productName FROM Product WHERE username = ?";
$products_stmt = $conn->prepare($products_sql);
$products_stmt->bind_param("s", $current_username);
$products_stmt->execute();
$products_result = $products_stmt->get_result();

$products = array();
while ($product_row = $products_result->fetch_assoc()) {
    $products[] = $product_row['productName'];
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
            <select id="productName" name="productName" required>
                <option value="">Select a product</option>
                <?php foreach($products as $product): ?>
                    <option value="<?php echo htmlspecialchars($product); ?>" 
                            <?php if($product === $deal['productName']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($product); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" id="distributorName" name="distributorName" 
                   value="<?php echo htmlspecialchars($deal['distributorName']); ?>" 
                   placeholder="Distributor Name" required>
            <input type="text" id="quantity" name="quantity" 
                   value="<?php echo htmlspecialchars($deal['quantity']); ?>" 
                   placeholder="Quantity" required>
            <input type="date" id="date" name="date" 
                   value="<?php echo date('Y-m-d', strtotime($deal['date'])); ?>" required>
            
            <input type="hidden" name="editDeal" value="1">
            
            <div class="button-group">
                <button type="submit">Save Changes</button>
                <a href="deals.php"><button type="button">Cancel</button></a>
            </div>
        </form>
    </div>
</body>
</html>