<?php
// Database connection details
$servername = "localhost";
$username = "root";  // Update with your database username
$password = "";      // Update with your database password
$dbname = "electronicdocument";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8 to handle special characters
$conn->set_charset("utf8");

// Optional: Check if connection is successful
// echo "Connected successfully";

?>
