<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Project/PHP/PHPProject.php to edit this template
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Signup and Login</title>
    </head>
    <body>
        <?php
        // Database connection
        $servername = "localhost";
        $username = "root"; // Default username for MAMP
        $password = "root"; // Default password for MAMP
        $dbname = "Wizmo";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Handle Sign Up form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $plain_password = $_POST['password']; // Plain-text password from the form
            $business = $_POST['business'];
            $category = $_POST['category'];
            $bio = $_POST['bio'];
            $location = $_POST['location'];

            // Hash the password
            $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

            // Handle file upload for the logo
            if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
                $target_dir = "../uploads"; // Directory to store uploaded files
                $target_file = $target_dir . basename($_FILES["file"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Check if the file is an actual image
                $check = getimagesize($_FILES["file"]["tmp_name"]);
                if ($check !== false) {
                    // Check file size (5MB max)
                    if ($_FILES["file"]["size"] <= 5000000) {
                        // Allow certain file formats
                        $allowed_types = ["jpg", "jpeg", "png", "gif"];
                        if (in_array($imageFileType, $allowed_types)) {
                            // Move the file to the target directory
                            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                                // Insert data into the Business table
                                $sql = "INSERT INTO Business (username, businessName, category, email, password, phoneNumber, city, description, logo)
                                        VALUES ('$name', '$business', '$category', '$email', '$hashed_password', '$phone', '$location', '$bio', '$target_file')";

                                if ($conn->query($sql) === TRUE) {
                                    // Redirect to Homepage.html
                                    header("Location: ../Homepage.html");
                                    exit();
                                } else {
                                    echo "Error: " . $sql . "<br>" . $conn->error;
                                }
                            } else {
                                echo "Sorry, there was an error uploading your file.";
                            }
                        } else {
                            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                        }
                    } else {
                        echo "Sorry, your file is too large. Maximum size is 5MB.";
                    }
                } else {
                    echo "File is not an image.";
                }
            } else {
                echo "No file uploaded or there was an error with the upload.";
                echo "Error Code: " . $_FILES["file"]["error"];
            }
        }

        // Handle Log In form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && !isset($_POST['name'])) {
            session_start(); // Start the session
            $email = $_POST['email'];
            $plain_password = $_POST['password']; // Plain-text password from the form

            // Fetch the hashed password from the database
            $sql = "SELECT * FROM Business WHERE email = '$email'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $hashed_password = $row['password'];

                // Verify the password
                if (password_verify($plain_password, $hashed_password)) {
                    // Store the username in the session
                    $_SESSION['username'] = $row['username'];

                    // Redirect to Homepage.html
                    header("Location: ../Homepage.html");
                    exit();
                } else {
                    echo "Invalid email or password.";
                }
            } else {
                echo "Invalid email or password.";
            }
        }

        $conn->close();
        ?>
    </body>
</html>