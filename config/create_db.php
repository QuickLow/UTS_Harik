<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE DATABASE uts"; 
if ($conn->query($sql) === TRUE) {
  echo "Database uts created successfully <br>";
  // Redirect to create_tbl.php
  header("Location: create_tbl.php");
} else {
  echo "Error creating database uts: " . $conn->error;
}

// Close connection
$conn->close();
?>