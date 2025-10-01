<?php
//create connection
include "<config>/conn_db.php";


// data to be insert
$id = $_POST['id'];
$name = $_POST['name'];
$price = $_POST['price'];
$description = $_POST['description'];



// query to update data into tabel products
$sql = "UPDATE products 
        SET name='$name',  price='$price', description='$description' WHERE id='$id'";


if ($conn->query($sql) === TRUE) {
    header("Location: read_tbl.php");
    exit();
} else {
  echo "Error updating record: " . $conn->error;
}

// Close connection
$conn->close();
?>