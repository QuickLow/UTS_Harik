<?php
// data to be insert
$prodName = $_POST['name'];
$prodDesc = $_POST['description'];
$prodPrice = $_POST['price'];


//create connection
include "<config>/conn_db.php";

// insert data into tabel products
$sql = "INSERT INTO products(name, description, price)
VALUES ('$prodName', '$prodDesc', '$prodPrice')";

if ($conn->query($sql) === TRUE) {
  echo "New record created successfully<br>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>