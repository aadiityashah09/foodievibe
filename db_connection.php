<?php
$servername = "127.0.0.1";// Use "127.0.0.1" or "localhost"
$username = "root";// Your database username
$password = ""; // Your database password (default for XAMPP is an empty string)
$dbname = "foodievibe"; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: {$conn->connect_error}");
}
