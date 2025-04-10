<?php
session_start(); // Start the session at the very beginning

// Check if user is logged in, redirect if not
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Database connection for product data
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Wizmo";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home page</title>
    <link rel="stylesheet" href="MainStyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
        </symbol></svg>
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
        /* New stock level bar styles */
        .stock-container {
            width: 100%;
            margin-top: 10px;
        }
        
        .stock-bar {
            height: 25px;
            width: 600px;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);
            background-color: #f0f0f0;
            margin: 5px 10px;
        }
        
        .stock-level {
            height: 100%;
            border-radius: 12px;
            transition: width 0.5s ease, background-color 0.5s ease;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
            text-shadow: 0 1px 1px rgba(0,0,0,0.3);
        }
        
        .stock-high {
            background: linear-gradient(to right, #4CAF50, #2E7D32);
        }
        
        .stock-medium {
            background: linear-gradient(to right, #FFC107, #FF9800);
        }
        
        .stock-low {
            background: linear-gradient(to right, #F44336, #D32F2F);
        }
        
        .stock-critical {
            background: linear-gradient(to right, #D32F2F, #B71C1C);
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        
        .stock-info {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-size: 14px;
            color: #666;
        }
        
        /* Updated product card styles */
        
        .product-list-container {
    margin-left: 70px; /* Adjusted margin-left */
    margin-right: 40px;
    padding: 20px;
    border: 5px outset #a85590;
    border-radius: 10px;
    background-color: #f9f9f9;
    max-width: 1000px;
    margin-top: 3px;
    margin-bottom: 20px;
}

.product-list-container h2 {
    text-align: center;
    color: #333;
    font-weight: bolder;
    font-size: 30px;
    margin-bottom: 20px;
    font-family: 'Times New Roman', Times, serif;
}
        .product-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            flex-grow: 0;   
        }
        
       
        
        .product-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            margin-top: 5px;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }
        
        .product-title {
             margin: 0;
    font-size: 18px;
    color: #333;
    background-color: #eaeaea;
    font-family: 'Times New Roman', Times, serif;
    font-weight: bold;
        }
        
        
    
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .product-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .product-image {
                margin-right: 0;
                margin-bottom: 10px;
            }
            
            .product-price {
                margin-top: 5px;
                align-self: flex-end;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div style="display: flex; align-items: center;">
                <a href="Homepage.php" style="display: inline-block;">
                    <img src="images/wizmoLOGO.png" alt="logo" width="90" height="80" class="Navimg">
                </a>
                <div class="text-container">
                    <h2>WIZMO</h2>
                    <h3>your smartest warehouse gizmo</h3>
                </div>
            </div>
            <div class="header-links">
                <a href="profile.php"><svg class="header-icon"><use href="#account"/></svg></a>
                <a href="Homepage.php"><svg class="header-icon"><use href="#home"/></svg></a>
                <a href="index.php"><svg class="header-icon"><use href="#logout"/></svg></a>
            </div>
        </div>
    </header>

    <!-- Navigation bar -->
    <nav class="navBar">
        <ul>
            <li class="link1"><a href="aboutus.php">About us</a></li>
            <li class="link2"><a href="Products.php">Products warehouse</a></li>
            <li class="link3"><a href="deals.php">Distributions deals</a></li>
            <li class="link4"><a href="community.php">Community</a></li>
            <li class="link5"><a href="request.php">My Requests</a></li>
        </ul>
    </nav>
    <main class="home-content">
      <aside class="home-sidebar">
        <div class="sidebar-box">
            <i class="fas fa-user sidebar-icon"></i>
            <h3>Hello <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
            <a href="profile.php"><button class="sidebar-button">Edit Profile</button></a>
        </div>
        <div class="sidebar-box">
            <i class="fas fa-globe sidebar-icon"></i>
            <h3>Find Collaboration</h3>
            <a href="request.php"><button class="sidebar-button">Find Collaboration</button></a>
        </div>
        <div class="sidebar-box">
            <i class="fas fa-tags sidebar-icon"></i>
            <h3>My Deals</h3>
            <a href="deals.php"><button class="sidebar-button">View Deals</button></a>
        </div>
    </aside>

      <section class="home-products">
        <div class="product-list-container">
            <h2>My products</h2>
            <?php
            // Fetch products for the logged-in user
            $loggedInUser = $_SESSION['username'];
            $sql = "SELECT * FROM Product WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $loggedInUser);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Extract quantity number from the quantity field (handles formats like "5 pcs", "8m", etc.)
                    $quantity = intval(preg_replace('/[^0-9]/', '', $row['quantity']));
                    
                    // Determine stock level class
                    if ($quantity >= 20) {
                        $stockClass = 'stock-high';
                        $stockPercentage = 100;
                    } elseif ($quantity >= 10) {
                        $stockClass = 'stock-medium';
                        $stockPercentage = ($quantity / 20) * 100;
                    } elseif ($quantity >= 5) {
                        $stockClass = 'stock-low';
                        $stockPercentage = ($quantity / 10) * 100;
                    } else {
                        $stockClass = 'stock-critical';
                        $stockPercentage = ($quantity / 5) * 100;
                    }
                    
                    // Ensure percentage doesn't exceed 100
                    $stockPercentage = min($stockPercentage, 100);
                    
                    echo '<div class="product-card">';
                    echo '<div class="product-header">';
                    
                    echo '<img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['productName']) . '" class="product-image">';
                    echo '<h3 class="product-title">' . htmlspecialchars($row['productName']) . '</h3>';
                    
                    echo '</div>';
                    
                    
                    
                    echo '<div class="stock-container">';
                    
                    echo '<div class="stock-bar">';
                    echo '<div class="stock-level ' . $stockClass . '" style="width:' . $stockPercentage . '%">';
                    echo $row['quantity'];
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="stock-info">';
                    echo '<span>Stock Level</span>';
                    echo '<span>' . $quantity . ' units</span>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products found.</p>';
            }
            
            // Close statement and connection
            $stmt->close();
            $conn->close();
            ?>
            <a href="Products.php" class="more-link"><h4>More details..</h4></a>
            
        </div>
          <a href="Products.php"><button class="products-button">Products Page</button></a>
    </section>
  </main>

     <!-- Footer -->
     <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="Homepage.php">Home</a></li>
                    <li><a href="aboutus.php">About Us</a></li>
                    <li><a href="Products.php">Products</a></li>
                    <li><a href="deals.php">Deals</a></li>
                    <li><a href="community.php">Community</a></li>
                    <li><a href="request.php">My Requests</a></li>
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
