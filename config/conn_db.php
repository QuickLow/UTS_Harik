<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uts";
// SET ZONA WAKTU AGAR SAMA DENGAN DATABASE (WIB)
date_default_timezone_set('Asia/Pontianak');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";
?>