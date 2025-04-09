
<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$connection = new mysqli("localhost", "root", "root", "Wizmo"); // Update credentials if needed
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Community</title>
        <link rel="stylesheet" href="style.css">
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
            body {
                font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
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

            <div class="header-links">
                <a href="profile.php"><svg class="header-icon"><use href="#account"/></svg></a>
                <a href="HomePage.php"><svg class="header-icon"><use href="#home"/></svg></a>
                <a href="login-signup.php"><svg class="header-icon"><use href="#logout"/></svg></a>
            </div>
        </div>
    </header>

    <nav class="navBar">
        <ul>
            <li class="link1"><a href="aboutus.php">About us</a></li>
            <li class="link2"><a href="Products.php">Products warehouse</a></li>
            <li class="link3"><a href="deals.php">Distributions deals</a></li>
            <li class="link4"><a href="community.php" class="active">Community</a></li>
            <li class="link5"><a href="request.php">Requests</a></li>
        </ul>
        
    </nav>

    <div class="container">
        <div class="filters">
            <select class="filter-select" id="categoryFilter">
                <option value="">Filter by Category</option>
                <?php
                $categoryQuery = "SELECT MIN(category) as category FROM Business GROUP BY LOWER(category) ORDER BY category ASC";

                $categoryResult = $connection->query($categoryQuery);
                while ($row = $categoryResult->fetch_assoc()) {
                    $category = htmlspecialchars($row['category']);
                    echo "<option value=\"$category\">$category</option>";
                }
                ?>
            </select>

            <select class="filter-select" id="cityFilter">
                <option value="">Filter by City</option>
                <?php
                $cityQuery = "SELECT MIN(city) as city FROM Business GROUP BY LOWER(city) ORDER BY city ASC";

                $cityResult = $connection->query($cityQuery);
                while ($row = $cityResult->fetch_assoc()) {
                    $city = htmlspecialchars($row['city']);
                    echo "<option value=\"$city\">$city</option>";
                }
                ?>
            </select>

        </div>

        <div class="business-list" id="businessList">
            <?php
            $query = "SELECT * FROM Business";
            $result = $connection->query($query);

            while ($row = $result->fetch_assoc()) {
                $category = htmlspecialchars($row['category']);
                $city = htmlspecialchars($row['city']);
                $businessName = htmlspecialchars($row['businessName']);
                $description = htmlspecialchars($row['description']);
                $logo = htmlspecialchars($row['logo']);
                echo "
                <div class='business-card' data-category='$category' data-city='$city'>
                    <img src='$logo' alt='$businessName Logo' class='business-logo'>
                    <div class='business-name'>$businessName</div>
                    <div class='business-category'>$category</div>
                    <div class='business-location'>$city</div>
                    <p>$description</p>
                    <button class='request-button'>Request a Deal</button>
                </div>";
            }
            ?>
        </div>

    </div>
    <div id="requestModal" class="modal">
        <div class="modal-content">
            <span id="closeModal" class="close">&times;</span>
            <h2>Request a Deal</h2>
            <form id="dealRequestForm">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
                
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required>
                
                <label for="businessName">Business Name</label>
                <input type="text" id="businessName" name="businessName" required>
                
                <label for="description">Order Description</label>
                <textarea id="description" name="description" required></textarea>
                
                <button type="submit" class="submit-button">Submit</button>
            </form>
        </div>
    </div>

    <script>

        

        document.getElementById('categoryFilter').addEventListener('change', filterBusinesses);
        document.getElementById('cityFilter').addEventListener('change', filterBusinesses);
        document.querySelectorAll('.request-button').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('requestModal').style.display = 'block';
            });
        });

        document.getElementById('closeModal').addEventListener('click', () => {
            document.getElementById('requestModal').style.display = 'none';
        });

        window.addEventListener('click', (event) => {
            if (event.target === document.getElementById('requestModal')) {
                document.getElementById('requestModal').style.display = 'none';
            }
        });

        document.getElementById('dealRequestForm').addEventListener('submit', (e) => {
            e.preventDefault(); 

            alert('Your request has been submitted successfully!');

            document.getElementById('requestModal').style.display = 'none';

            e.target.reset();
        });
function filterBusinesses() {
    const category = document.getElementById('categoryFilter').value;
    const city = document.getElementById('cityFilter').value;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `fetchBusinesses.php?category=${encodeURIComponent(category)}&city=${encodeURIComponent(city)}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById('businessList').innerHTML = xhr.responseText;
            addRequestButtonListeners(); 
        }
    };
    xhr.send();
}
    </script>
<footer>
        <div class="footer-container">
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="HomePage.html">Home</a></li>
                    <li><a href="aboutus.html">About Us</a></li>
                    <li><a href="Products.html">Products</a></li>
                    <li><a href="deals.html">Deals</a></li>
                    <li><a href="community.html">Community</a></li>
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
