<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uts"; // Pastikan terhubung ke database 'uts'

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// 1. SQL UNTUK MEMBUAT TABEL PENGGUNA (USERS)
$sql_users = "CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin_gudang') NOT NULL DEFAULT 'admin_gudang',
    status INT(1) NOT NULL DEFAULT 0,
    activation_token VARCHAR(64) NULL,
    reset_token VARCHAR(64) NULL,
    reset_token_expiry DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (email)
)";

if ($conn->query($sql_users) === TRUE) {
  echo "Table 'users' created successfully<br>";
} else {
  echo "Error creating table 'users': " . $conn->error . "<br>";
}

// 2. SQL UNTUK MEMBUAT TABEL PRODUK (PRODUCTS)
$sql_products = "CREATE TABLE products (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    price DOUBLE NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
)";

if ($conn->query($sql_products) === TRUE) {
  echo "Table 'products' created successfully<br>";
} else {
  echo "Error creating table 'products': " . $conn->error . "<br>";
}

$conn->close();
?>