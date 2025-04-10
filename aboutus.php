<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Wizmo</title>
    <link rel="stylesheet" href="MainStyles.css">
    <!-- Added Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cambria:wght@400;700&family=Impact&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3348;
            --secondary-color: #800080;
            --accent-color: #e47a8f;
            --light-bg: #f8f8f8;
            --gradient: linear-gradient(130deg, #800080, #e47a8f);
            --text-dark: #555;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text-dark);
            line-height: 1.6;
            font-family: 'Poppins', sans-serif;
        }

        /* Header styles inherit from MainStyles.css */

        /* Main Content Styles */
        main {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            font-size: 40px;
            color: var(--primary-color);
            margin-bottom: 12px;
        }

        .page-header p {
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            font-size: 20px;
            color: var(--text-dark);
            max-width: 700px;
            margin: 0 auto;
        }

        .card {
            background: linear-gradient(135deg, #e0f2f7, #f0e6ef);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid #ddd;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
            padding: 30px;
        }

        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .card-header {
            padding: 20px 0;
            position: relative;
            text-align: center;
        }

        .card-header h2 {
            font-size: 2.5em;
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 30px;
        }

        .card-body {
            padding: 20px 0;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .feature-item {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            transition: transform 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-5px);
        }

        .feature-item h3 {
            color: var(--secondary-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            font-weight: 700;
        }

        .feature-icon {
            background-color: rgba(128, 0, 128, 0.1);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        .feature-icon svg {
            width: 24px;
            height: 24px;
            fill: var(--secondary-color);
        }

        .feature-item ul {
            list-style: none;
            padding-left: 10px;
        }

        .feature-item li {
            margin-bottom: 10px;
            position: relative;
            padding-left: 25px;
            color: var(--text-dark);
            font-size: 1em;
        }

        .feature-item li::before {
            content: "✓";
            color: var(--secondary-color);
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .vision-card {
            background: linear-gradient(135deg, #e8f2f5, #f0e6ef);
            padding: 40px;
            border-radius: var(--border-radius);
            margin-top: 50px;
            box-shadow: var(--box-shadow);
            border: 1px solid #ddd;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }

        .vision-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .vision-card h2 {
            font-size: 2.5em;
            color: var(--secondary-color);
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
        }

        .vision-card p {
            line-height: 1.7;
            font-size: 1.2em;
            color: #444;
            margin-bottom: 30px;
        }

        .vision-card p:last-child {
            margin-bottom: 0;
        }

        /* Action button - matches the sidebar-button style */
        .action-button {
            background: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            font-size: 17px;
            font-weight: 800;
            transition: background 0.3s ease, transform 0.2s ease-in-out;
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            text-align: center;
        }

        .action-button:hover {
            background: var(--gradient);
            transform: scale(1.05);
        }

        /* Footer styles inherit from MainStyles.css */

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .feature-grid {
                grid-template-columns: 1fr;
            }
            
            .card, .vision-card {
                padding: 20px;
                width: 100%;
            }
            
            .page-header h1 {
                font-size: 32px;
            }
            
            .card-header h2, .vision-card h2 {
                font-size: 2em;
            }
        }
    </style>
    <!-- SVG Icons -->
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
        <symbol id="warehouse" viewBox="0 0 24 24">
          <path d="M22 21h-2V7h2v14zm-4 0H6V7h12v14zm-14 0H2V7h2v14zM12 3l10 4v2H2V7l10-4z"/>
        </symbol>
        <symbol id="deals" viewBox="0 0 24 24">
          <path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58s1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41s-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/>
        </symbol>
        <symbol id="community" viewBox="0 0 24 24">
          <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
        </symbol>
        <symbol id="requests" viewBox="0 0 24 24">
          <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
        </symbol>
    </svg>
</head>
<body>
    <?php
    // PHP code would go here (session management, database connections, etc.)
    ?>

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
            <li class="link1"><a href="aboutus.php" class="active">About us</a></li>
            <li class="link2"><a href="Products.php">Products warehouse</a></li>
            <li class="link3"><a href="deals.php">Distributions deals</a></li>
            <li class="link4"><a href="community.php">Community</a></li>
            <li class="link5"><a href="request.php">My Requests</a></li>
        </ul>
    </nav>

    <main>
        
        
        
        <div class="page-header">
            <h1>About Wizmo</h1>
            <p>Streamlining warehouse management for small and medium enterprises</p>
        </div>
        
        <div class="vision-card">
            <h2>Our Vision</h2>
            <p>Our vision is to empower Small and Medium-sized Enterprises (SMEs) by revolutionizing their warehouse operations. We aim to eliminate the challenges of inefficient inventory management, fragmented distribution, and isolated operations by providing a streamlined, collaborative platform.</p>
            <p>Through our Warehouse Management System, SMEs will achieve real-time stock visibility, seamless product distribution, and a thriving community network, ultimately minimizing stock shortages, optimizing resources, and fostering local business collaboration.</p>
            <div style="text-align: center;">
                <a href="community.php" class="action-button">Join Our Community</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Who We Are</h2>
            </div>
            <div class="card-body">
                <p>Wizmo is a user-friendly platform designed to streamline warehouse inventory management for Small and Medium-sized Enterprises (SMEs). Our comprehensive solution addresses the complex challenges that businesses face with inventory tracking, distribution management, and business collaboration.</p>
                
                <div class="feature-grid">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg><use href="#warehouse"/></svg>
                        </div>
                        <h3>Product Warehouse</h3>
                        <ul>
                            <li>Add & store all your products</li>
                            <li>Edit or delete product information</li>
                            <li>Receive alerts for low stock levels</li>
                            <li>Track inventory in real-time</li>
                        </ul>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg><use href="#deals"/></svg>
                        </div>
                        <h3>Distribution Deals</h3>
                        <ul>
                            <li>Add external distributor details</li>
                            <li>Track distribution agreements</li>
                            <li>Manage supply chain connections</li>
                            <li>Optimize distribution networks</li>
                        </ul>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg><use href="#community"/></svg>
                        </div>
                        <h3>Community</h3>
                        <ul>
                            <li>Find businesses in different categories</li>
                            <li>Connect with companies in your city</li>
                            <li>Request business collaborations</li>
                            <li>Build your professional network</li>
                        </ul>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg><use href="#requests"/></svg>
                        </div>
                        <h3>Requests</h3>
                        <ul>
                            <li>View and manage incoming requests</li>
                            <li>Accept or decline collaboration offers</li>
                            <li>Track accepted partnerships</li>
                            <li>Maintain business relationships</li>
                        </ul>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <a href="Products.php" class="action-button">Explore Our Platform</a>
                </div>
            </div>
        </div>

        
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

    <?php
    // Additional PHP code could go here (analytics, etc.)
    ?>
</body>
</html>
