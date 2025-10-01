<?php
include 'config/conn_db.php';

$sql = "SELECT name, description, price, created FROM products";
$result = $conn->query($sql);


if ($result->num_rows> 0) {
  //output data of each row
  while ($row = $result->fetch_assoc()) {
    // body of loop
    echo "Name: " . $row["name"]." - Description: ". $row["description"]." - Price: ". $row["price"]." - Created: ". $row["created"]."<br>";    
  }
} else {
  echo "0 result - Data not found";
}

// Close connection
$conn->close();
?>