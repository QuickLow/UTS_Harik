<?php
//  insert data user
$usr = $_POST['username'];
$psw = $_POST['password'];
$fname = $_POST['fullname'];
$role = $_POST['role'];


//create connection
include "../<config>/conn_db.php";

// insert data into tabel users
$sql = "INSERT INTO users(username, password, fullname, role)
VALUES ('$usr', '$psw', '$fname', '$role')";

if ($conn->query($sql) === TRUE) {
  echo "New account created successfully<br>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>