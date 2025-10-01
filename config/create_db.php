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


if ($conn->query($sql) === TRUE) {
  echo "Database db_webpro5d created successfully <br>";
  // Redirect to create_tbl.php
  header("Location: create_tbl.php");
} else {
  echo "Error creating database db_webpro5d: " . $conn->error;
}

// Close connection
$conn->close();
?>