<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$connection = new mysqli("localhost", "root", "root", "Wizmo", 8889);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$currentUser = $_SESSION['username'];

// Handle AJAX update for Accept/Decline
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action'], $_POST['username'], $_POST['message'])
) {
    $senderUsername = $_POST['username'];
    $message = $_POST['message'];
    $action = $_POST['action'];
    $newState = $action === 'accept' ? 'Accepted' : 'Declined';

    $stmt = $connection->prepare("UPDATE Request SET state = ? WHERE username = ? AND receiverUsername = ? AND message = ?");
    $stmt->bind_param("ssss", $newState, $senderUsername, $currentUser, $message);
    $stmt->execute();
    echo 'success';
    exit();
}

// Get requests received by the current user
$receivedQuery = "SELECT r.message, r.username, r.state, b.businessName, b.logo, b.description, b.phoneNumber 
          FROM Request r 
          JOIN Business b ON r.username = b.username 
          WHERE r.receiverUsername = ?";

$stmt = $connection->prepare($receivedQuery);
$stmt->bind_param("s", $currentUser);
$stmt->execute();
$receivedResult = $stmt->get_result();

$pendingReceived = [];
$acceptedReceived = [];
while ($row = $receivedResult->fetch_assoc()) {
    if ($row['state'] === 'Accepted') {
        $acceptedReceived[] = $row;
    } elseif ($row['state'] === 'Pending') {
        $pendingReceived[] = $row;
    }
}

// Get requests sent by the current user
$sentQuery = "SELECT r.message, r.receiverUsername, r.state, b.businessName, b.logo, b.description, b.phoneNumber 
          FROM Request r 
          JOIN Business b ON r.receiverUsername = b.username 
          WHERE r.username = ?";

$stmt = $connection->prepare($sentQuery);
$stmt->bind_param("s", $currentUser);
$stmt->execute();
$sentResult = $stmt->get_result();

$pendingSent = [];
$acceptedSent = [];
while ($row = $sentResult->fetch_assoc()) {
    if ($row['state'] === 'Accepted') {
        $acceptedSent[] = $row;
    } elseif ($row['state'] === 'Pending') {
        $pendingSent[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Requests</title>
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
            background-color: #f8f8f8;
        }
        .request, .accepted {
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            margin-right: 40px;
            margin-left: 40px;
        }
        .request img, .accepted img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }
        .phone-number {
            font-weight: bold;
            color: #1e3348;
            margin-top: 5px;
        }
        button {
            background-color: #1e3348;
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            font-size: 17px;
            font-weight: 800;
            color: white;
            text-decoration: none;
            display: inline-block;
            position: relative;
            border-radius: 25px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            z-index: 1;
            margin-left: auto;
            padding: 5px 10px;
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
        h2 {
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            margin: 20px;
        }
        .section {
            margin-bottom: 30px;
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
                <a href="Homepage.php"><svg class="header-icon"><use href="#home"/></svg></a>
                <a href="index.php"><svg class="header-icon"><use href="#logout"/></svg></a>
            </div>
        </div>
    </header>

    <nav class="navBar">
        <ul>
            <li class="link1"><a href="aboutus.php">About us</a></li>
            <li class="link2"><a href="Products.php">Products warehouse</a></li>
            <li class="link3"><a href="deals.php">Distributions deals</a></li>
            <li class="link4"><a href="community.php">Community</a></li>
            <li class="link5"><a href="request.php" class="active">Requests</a></li>
        </ul>
    </nav>

    <div class="section">
        <h2>Incoming Collaboration Requests</h2>
        <div id="receivedRequests">
            <?php if (empty($pendingReceived)): ?>
                <p style="margin-left: 40px;">No pending requests.</p>
            <?php else: ?>
                <?php foreach ($pendingReceived as $row): ?>
                    <div class="request" data-username="<?= htmlspecialchars($row['username']) ?>" data-message="<?= htmlspecialchars($row['message']) ?>">
                        <img src="<?= htmlspecialchars($row['logo']) ?>" alt="<?= htmlspecialchars($row['businessName']) ?>">
                        <div>
                            <strong><?= htmlspecialchars($row['businessName']) ?></strong>
                            <p><?= htmlspecialchars($row['description']) ?></p>
                            <p><?= htmlspecialchars($row['message']) ?></p>
                        </div>
                        <button onclick="updateRequestState('accept', '<?= htmlspecialchars($row['username']) ?>', '<?= htmlspecialchars($row['message']) ?>')">Accept</button>
                        <button onclick="updateRequestState('decline', '<?= htmlspecialchars($row['username']) ?>', '<?= htmlspecialchars($row['message']) ?>')">Decline</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="section">
        <h2>My Sent Requests</h2>
        <div id="sentRequests">
            <?php if (empty($pendingSent)): ?>
                <p style="margin-left: 40px;">No pending sent requests.</p>
            <?php else: ?>
                <?php foreach ($pendingSent as $row): ?>
                    <div class="request">
                        <img src="<?= htmlspecialchars($row['logo']) ?>" alt="<?= htmlspecialchars($row['businessName']) ?>">
                        <div>
                            <strong><?= htmlspecialchars($row['businessName']) ?></strong>
                            <p><?= htmlspecialchars($row['description']) ?></p>
                            <p><?= htmlspecialchars($row['message']) ?></p>
                            <p><em>Status: Pending</em></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="section">
        <h2>Accepted Partnerships</h2>
        <div id="acceptedPartnerships">
            <?php if (empty($acceptedReceived) && empty($acceptedSent)): ?>
                <p style="margin-left: 40px;">No accepted partnerships yet.</p>
            <?php else: ?>
                <?php foreach ($acceptedReceived as $row): ?>
                    <div class="accepted">
                        <img src="<?= htmlspecialchars($row['logo']) ?>" alt="<?= htmlspecialchars($row['businessName']) ?>">
                        <div>
                            <strong><?= htmlspecialchars($row['businessName']) ?></strong>
                            <p><?= htmlspecialchars($row['description']) ?></p>
                            <p><?= htmlspecialchars($row['message']) ?></p>
                            <p class="phone-number">Contact: <?= htmlspecialchars($row['phoneNumber']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php foreach ($acceptedSent as $row): ?>
                    <div class="accepted">
                        <img src="<?= htmlspecialchars($row['logo']) ?>" alt="<?= htmlspecialchars($row['businessName']) ?>">
                        <div>
                            <strong><?= htmlspecialchars($row['businessName']) ?></strong>
                            <p><?= htmlspecialchars($row['description']) ?></p>
                            <p><?= htmlspecialchars($row['message']) ?></p>
                            <p class="phone-number">Contact: <?= htmlspecialchars($row['phoneNumber']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function updateRequestState(action, username, message) {
        fetch("request.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `action=${action}&username=${encodeURIComponent(username)}&message=${encodeURIComponent(message)}`
        })
        .then(response => response.text())
        .then(() => {
            location.reload();
        });
    }
    </script>

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
                    <li><a href="request.php" class="active">Requests</a></li>
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