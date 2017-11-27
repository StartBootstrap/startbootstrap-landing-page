<?php
$servername = "localhost";
$username = "root";
$password = "logarithmssszxc123";
$database = "pause";
// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
