<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$connection = new mysqli("localhost", "root", "root", "Wizmo");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Community</title>
    <link rel="stylesheet" href="style.css">
    <style>body { font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; }</style>
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
            <a href="index.php"><svg class="header-icon"><use href="#logout"/></svg></a>
        </div>
    </div>
</header>

<nav class="navBar">
    <ul>
        <li class="link1"><a href="aboutus.php">About us</a></li>
        <li class="link2"><a href="Products.php">Products warehouse</a></li>
        <li class="link3"><a href="deals.php">Distributions deals</a></li>
        <li class="link4"><a href="community.php" class="active">Community</a></li>
        <li class="link5"><a href="request.html">Requests</a></li>
    </ul>
</nav>

<div class="container">
    <div class="filters">
        <select class="filter-select" id="categoryFilter">
            <option value="">Filter by Category</option>
            <?php
            $categoryQuery = "SELECT category FROM Business";
            $categoryResult = $connection->query($categoryQuery);
            $seenCategories = [];
            while ($row = $categoryResult->fetch_assoc()) {
                $original = $row['category'];
                $normalized = strtolower(trim($original));
                if (!isset($seenCategories[$normalized])) {
                    $seenCategories[$normalized] = $original;
                }
            }
            foreach ($seenCategories as $category) {
                $categoryEscaped = htmlspecialchars(trim($category));
                echo "<option value=\"$categoryEscaped\">$categoryEscaped</option>";
            }
            ?>
        </select>

        <select class="filter-select" id="cityFilter">
            <option value="">Filter by City</option>
            <?php
            $cityQuery = "SELECT city FROM Business";
            $cityResult = $connection->query($cityQuery);
            $seenCities = [];
            while ($row = $cityResult->fetch_assoc()) {
                $original = $row['city'];
                $normalized = strtolower(trim($original));
                if (!isset($seenCities[$normalized])) {
                    $seenCities[$normalized] = $original;
                }
            }
            foreach ($seenCities as $city) {
                $cityEscaped = htmlspecialchars(trim($city));
                echo "<option value=\"$cityEscaped\">$cityEscaped</option>";
            }
            ?>
        </select>
    </div>

    <div class="business-list" id="businessList">
        <?php
        $currentUser = $_SESSION['username'];
        $query = "SELECT * FROM Business WHERE username != '$currentUser'";

        $result = $connection->query($query);
        while ($row = $result->fetch_assoc()) {
            $category = htmlspecialchars($row['category']);
            $city = htmlspecialchars($row['city']);
            $businessName = htmlspecialchars($row['businessName']);
            $description = htmlspecialchars($row['description']);
            $logo = htmlspecialchars($row['logo']);
            $businessUsername = htmlspecialchars($row['username']);
            echo "
            <div class='business-card' data-category='$category' data-city='$city'>
                <img src='$logo' alt='$businessName Logo' class='business-logo'>
                <div class='business-name'>$businessName</div>
                <div class='business-category'>$category</div>
                <div class='business-location'>$city</div>
                <p>$description</p>
                <button class='request-button' data-username='$businessUsername'>Request a Deal</button>
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
            
            <textarea id="message" name="message" rows="12" placeholder="Enter your message to the business..." required></textarea>
            <input type="hidden" id="receiverUsername" name="receiverUsername">
            <input type="hidden" id="senderUsername" name="senderUsername" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
            <button type="submit" class="submit-button">Submit</button>
        </form>
    </div>
</div>

<script>
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

document.getElementById('categoryFilter').addEventListener('change', filterBusinesses);
document.getElementById('cityFilter').addEventListener('change', filterBusinesses);

function addRequestButtonListeners() {
    document.querySelectorAll('.request-button').forEach(button => {
        button.addEventListener('click', () => {
            const username = button.getAttribute('data-username');
            document.getElementById('receiverUsername').value = username;
            document.getElementById('requestModal').style.display = 'block';
        });
    });
}
addRequestButtonListeners();

document.getElementById('dealRequestForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    fetch('submitRequest.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        if (result.trim() === "success") {
            alert('Your request has been submitted successfully!');
            document.getElementById('requestModal').style.display = 'none';
            e.target.reset();
        } else {
            alert('Something went wrong while submitting your request.');
        }
    })
    .catch(error => {
        alert('Error: ' + error);
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
