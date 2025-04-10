<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Wizmo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Sign Up form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
    // Validate inputs
    $errors = [];
    
    $name = trim($_POST['name']);
    if (empty($name)) {
        $errors['name'] = "Name is required";
    }
    
    $email = trim($_POST['email']);
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address";
    }
    
    $phone = trim($_POST['phone']);
    if (empty($phone)) {
        $errors['phone'] = "Phone number is required";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors['phone'] = "Please enter a valid phone number (10-15 digits)";
    }
    
    // Only proceed if no validation errors
    if (empty($errors)) {
        $business = $_POST['business'];
        $category = $_POST['category'];
        $bio = $_POST['bio'];
        $location = $_POST['location'];
        $plain_password = $_POST['password'];
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

        // Handle file upload
        if (isset($_FILES['file'])) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["file"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $uploadOk = true;
            $errorMsg = "";
            
            $check = getimagesize($_FILES["file"]["tmp_name"]);
            if ($check === false) {
                $errorMsg = "File is not an image.";
                $uploadOk = false;
            }
            
            if ($_FILES["file"]["size"] > 5000000) {
                $errorMsg = "Sorry, your file is too large. Maximum size is 5MB.";
                $uploadOk = false;
            }
            
            $allowed_types = ["jpg", "jpeg", "png", "gif"];
            if (!in_array($imageFileType, $allowed_types)) {
                $errorMsg = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = false;
            }
            
            if ($uploadOk) {
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                    $sql = "INSERT INTO Business (username, businessName, category, email, password, phoneNumber, city, description, logo)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssssss", $name, $business, $category, $email, $hashed_password, $phone, $location, $bio, $target_file);

                    if ($stmt->execute()) {
                        $_SESSION['username'] = $name;
                        header("Location: Homepage.php");
                        exit();
                    } else {
                        $signupError = "Error: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $signupError = "Sorry, there was an error uploading your file.";
                }
            } else {
                $signupError = $errorMsg;
            }
        } else {
            $signupError = "No file uploaded or there was an error with the upload.";
        }
    } else {
        $signupError = "Please correct the errors below.";
    }
}

// Handle Log In form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && !isset($_POST['name'])) {
    $email = $_POST['email'];
    $plain_password = $_POST['password'];

    $sql = "SELECT * FROM Business WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

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
    $stmt->close();
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
    <style>
        .error-message {
            color: red;
            font-size: 0.8rem;
            margin-top: -10px;
            margin-bottom: 10px;
        }
        input.error {
            border-color: red !important;
        }
        .step2 {
            display: none;
        }
    </style>
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
                    <input type="text" placeholder="Name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" <?php if (isset($errors['name'])) echo 'class="error"'; ?> required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="error-message"><?php echo $errors['name']; ?></div>
                    <?php endif; ?>
                    
                    <input type="email" placeholder="Email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" <?php if (isset($errors['email'])) echo 'class="error"'; ?> required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="error-message"><?php echo $errors['email']; ?></div>
                    <?php endif; ?>
                    
                    <input type="tel" placeholder="Phone Number" name="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" <?php if (isset($errors['phone'])) echo 'class="error"'; ?> required>
                    <?php if (isset($errors['phone'])): ?>
                        <div class="error-message"><?php echo $errors['phone']; ?></div>
                    <?php endif; ?>
                    
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
                    <label for="file">Business Logo</label>
                    <input placeholder="Logo" type="file" name="file" required>

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

        nextStepBtn.addEventListener('click', () => {
            // Get all input fields
            const nameInput = document.querySelector('input[name="name"]');
            const emailInput = document.querySelector('input[name="email"]');
            const phoneInput = document.querySelector('input[name="phone"]');
            
            // Reset previous error styles
            [nameInput, emailInput, phoneInput].forEach(input => {
                input.classList.remove('error');
                const errorElement = input.nextElementSibling;
                if (errorElement && errorElement.classList.contains('error-message')) {
                    errorElement.remove();
                }
            });
            
            let isValid = true;
            
            // Validate Name - just check if not empty
            if (!nameInput.value.trim()) {
                showError(nameInput, 'Name is required');
                isValid = false;
            }
            
            // Validate Email - check format
            if (!emailInput.value.trim()) {
                showError(emailInput, 'Email is required');
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
                showError(emailInput, 'Please enter a valid email address');
                isValid = false;
            }
            
            // Validate Phone - simple digit check
            if (!phoneInput.value.trim()) {
                showError(phoneInput, 'Phone number is required');
                isValid = false;
            } else if (!/^[0-9]{10,15}$/.test(phoneInput.value.trim())) {
                showError(phoneInput, 'Please enter a valid phone number (10-15 digits)');
                isValid = false;
            }
            
            if (isValid) {
                step1.style.display = 'none';
                step2.style.display = 'block';
            }
        });

        prevStepBtn.addEventListener('click', () => {
            step2.style.display = 'none';
            step1.style.display = 'block';
        });

        // Helper function to show error messages
        function showError(input, message) {
            input.classList.add('error');
            const errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.style.color = 'red';
            errorElement.style.fontSize = '0.8rem';
            errorElement.style.marginTop = '-10px';
            errorElement.style.marginBottom = '10px';
            errorElement.textContent = message;
            input.parentNode.insertBefore(errorElement, input.nextSibling);
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>