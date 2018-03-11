<?php
$servername = "localhost";
$username = "vdi_user";
$password = "eaamb78£";
$dbname = "vehicle_daily_inspection";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 
?>