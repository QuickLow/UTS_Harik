<?php
// create connection
include "<config>/conn_db.php";

//delete record on table
$sql = "DELETE from products WHERE id=$_GET[id]";


if ($conn->query($sql) === TRUE) {
//   echo "New record created successfully<br>";
    header("Location: read_tbl.php");
    exit();
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();

?>