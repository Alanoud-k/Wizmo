<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Start the session and handle form submissions first
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Wizmo";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, 8889);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Sign Up form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $plain_password = $_POST['password'];
    $business = $_POST['business'];
    $category = $_POST['category'];
    $bio = $_POST['bio'];
    $location = $_POST['location'];

    // Hash the password
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

    // Handle file upload for the logo
    if (isset($_FILES['file'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate and move the uploaded file
        $uploadOk = true;
        $errorMsg = "";
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["file"]["tmp_name"]);
        if ($check === false) {
            $errorMsg = "File is not an image.";
            $uploadOk = false;
        }
        
        // Check file size (5MB max)
        if ($_FILES["file"]["size"] > 5000000) {
            $errorMsg = "Sorry, your file is too large. Maximum size is 5MB.";
            $uploadOk = false;
        }
        
        // Allow certain file formats
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowed_types)) {
            $errorMsg = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = false;
        }
        
        if ($uploadOk) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                // Insert data into the Business table
                $sql = "INSERT INTO Business (username, businessName, category, email, password, phoneNumber, city, description, logo)
                        VALUES ('$name', '$business', '$category', '$email', '$hashed_password', '$phone', '$location', '$bio', '$target_file')";

                if ($conn->query($sql) === TRUE) {
                    $_SESSION['username'] = $name;
                    header("Location: Homepage.php");
                    exit();
                } else {
                    $signupError = "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                $signupError = "Sorry, there was an error uploading your file.";
            }
        } else {
            $signupError = $errorMsg;
        }
    } else {
        $signupError = "No file uploaded or there was an error with the upload.";
    }
}

// Handle Log In form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && !isset($_POST['name'])) {
    $email = $_POST['email'];
    $plain_password = $_POST['password'];

    $sql = "SELECT * FROM Business WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($plain_password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            header("Location:Homepage.php");
            exit();
        } else {
            $loginError = "Invalid email or password.";
        }
    } else {
        $loginError = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="login.css">
    <title>Login page</title>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form id="signupForm" method="POST" enctype="multipart/form-data">
                <!-- Step 1 -->
                <div class="step1">
                    <h1>Create Account</h1>
                    <?php if (isset($signupError)): ?>
                        <div class="error-message" style="color: red; margin-bottom: 15px;"><?php echo $signupError; ?></div>
                    <?php endif; ?>
                    <div class="social-icons">
                        <a href="https://www.instagram.com" class="icon"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://www.facebook.com" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://twitter.com" class="icon"><i class="fa-brands fa-twitter"></i></a>
                        <a href="https://www.linkedin.com" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                    <span>Enter your personal information, or use your email for registration.</span>
                    <input type="text" placeholder="Name" name="name" required>
                    <input type="email" placeholder="Email" name="email" required>
                    <input type="tel" placeholder="Phone Number" name="phone" required>
                    <input type="password" placeholder="Password" name="password" required>
                    <button type="button" id="nextStep">Next</button>
                </div>

                <!-- Step 2 -->
                <div class="step2">
                    <h1>Create Account</h1>
                    <div class="social-icons">
                        <a href="https://www.instagram.com" class="icon"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://www.facebook.com" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://twitter.com" class="icon"><i class="fa-brands fa-twitter"></i></a>
                        <a href="https://www.linkedin.com" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                    <span>Almost there! Now your business details.</span>
                    <input type="text" placeholder="Business Name" name="business" required>

<!-- Dropdown for Business Category -->
<label for="category">Business Category</label>
<select name="category" id="category" required>
  <option value="">-- Select Business Category --</option>
  <option value="agriculture">Agriculture</option>
  <option value="construction">Construction</option>
  <option value="education">Education</option>
  <option value="entertainment">Entertainment</option>
  <option value="fashion">Fashion & Beauty</option>
  <option value="food">Food & Beverage</option>
  <option value="health">Health & Wellness</option>
  <option value="logistics">Logistics & Delivery</option>
  <option value="manufacturing">Manufacturing</option>
  <option value="retail">Retail</option>
  <option value="services">Professional Services</option>
  <option value="tech">Technology</option>
  <option value="tourism">Tourism & Hospitality</option>
  <option value="other">other</option>

</select>

<input type="text" placeholder="Bio/Description" name="bio" required>

<!-- Dropdown for City -->
<label for="location">City</label>
<select name="location" id="location" required>
  <option value="">-- Select City --</option>
  <option value="abha">Abha</option>
  <option value="albahah">Al-Bahah</option>
  <option value="alhasa">Al-Hasa</option>
  <option value="alula">AlUla</option>
  <option value="arara">Arar</option>
  <option value="buraydah">Buraydah</option>
  <option value="dammam">Dammam</option>
  <option value="hail">Hail</option>
  <option value="jazan">Jazan</option>
  <option value="jeddah">Jeddah</option>
  <option value="khobar">Khobar</option>
  <option value="madinah">Madinah</option>
  <option value="makkah">Makkah</option>
  <option value="najran">Najran</option>
  <option value="qurayyat">Al Qurayyat</option>
  <option value="riyadh">Riyadh</option>
  <option value="sakaka">Sakaka</option>
  <option value="tabuk">Tabuk</option>
  <option value="taif">Taif</option>
  <option value="yanbu">Yanbu</option>
</select>

<input type="file" name="file" required>

<button type="button" id="prevStep">Previous</button>
<button type="submit">Sign Up</button>
                </div>
            </form>
        </div>
        <div class="form-container sign-in">
            <form id="signinForm" method="POST">
                <h1>Log In</h1>
                <?php if (isset($loginError)): ?>
                    <div class="error-message" style="color: red; margin-bottom: 15px;"><?php echo $loginError; ?></div>
                <?php endif; ?>
                <div class="social-icons">
                    <a href="https://www.instagram.com" class="icon"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://www.facebook.com" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://twitter.com" class="icon"><i class="fa-brands fa-twitter"></i></a>
                    <a href="https://www.linkedin.com/in/yourusername" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>or use your email password</span>
                <input type="email" placeholder="Email" name="email" required>
                <input type="password" placeholder="Password" name="password" required>
                <a href="#">Forget Your Password?</a>
                <button type="submit">Log In</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <img src="images/wizmoLOGO.png" alt="logo" width="90" height="80">
                    <h1>Welcome Back!</h1>
                    <p>Please enter your personal details to continue managing your warehouse.</p>
                    <button class="hidden" id="login">Log In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <img src="images/wizmoLOGO.png" alt="logo" width="90" height="80">
                    <h1>Hello, Friend!</h1>
                    <p>To begin managing your warehouse, please provide the following information.</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');
        const registerBtn = document.getElementById('register');
        const loginBtn = document.getElementById('login');

        registerBtn.addEventListener('click', () => {
            container.classList.add("active");
        });

        loginBtn.addEventListener('click', () => {
            container.classList.remove("active");
        });

        // Multi-step form logic
        const nextStepBtn = document.getElementById('nextStep');
        const prevStepBtn = document.getElementById('prevStep');
        const step1 = document.querySelector('.step1');
        const step2 = document.querySelector('.step2');

        // Initially hide step 2
        step2.style.display = 'none';

        nextStepBtn.addEventListener('click', () => {
            // Validate step 1 fields before proceeding
            const inputs = step1.querySelectorAll('input[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = 'red';
                } else {
                    input.style.borderColor = '';
                }
            });

            if (isValid) {
                step1.style.display = 'none';
                step2.style.display = 'block';
            }
        });

        prevStepBtn.addEventListener('click', () => {
            step2.style.display = 'none';
            step1.style.display = 'block';
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>