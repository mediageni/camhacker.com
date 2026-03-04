<?php
// Database connection details
$host = 'localhost';
$dbname = 'u508463306_cams';
$username = 'u508463306_camman';
$password = '564HHGlkjm&';

// Create MySQL connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
