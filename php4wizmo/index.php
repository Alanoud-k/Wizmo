<!DOCTYPE html>
<!--
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Project/PHP/PHPProject.php to edit this template
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
     
// Database<?php
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
    $password = $_POST['password'];
    $business = $_POST['business'];
    $category = $_POST['category'];
    $bio = $_POST['bio'];
    $location = $_POST['location'];

    // Insert data into the Business table
    $sql = "INSERT INTO Business (username, businessName, category, email, password, phoneNumber, city, description, logo)
            VALUES ('$name', '$business', '$category', '$email', '$password', '$phone', '$location', '$bio', '')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to Homepage.html
        header("Location: ../Homepage.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle Log In form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && !isset($_POST['name'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate user credentials (example only, use prepared statements for security)
    $sql = "SELECT * FROM Business WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Redirect to Homepage.html
        header("Location: ../Homepage.html");
        exit();
    } else {
        echo "Invalid email or password.";
    }
}

$conn->close();
?>
    </body>
</html>
