<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_webpro5d";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// sql to create table product
$sql = "CREATE TABLE users(
id INT(11) NOT NULL AUTO_INCREMENT,
username VARCHAR(50) NOT NULL UNIQUE,
password VARCHAR (50) NOT NULL,
fullname VARCHAR (50) NOT NULL,
role VARCHAR (50) NOT NULL,
status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
reg_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (id) 
)";

if ($conn->query($sql) === TRUE) {
  echo "Table products created successfully";
} else {
  echo "Error creating table products: " . $conn->error;
}


$conn->close();
?>