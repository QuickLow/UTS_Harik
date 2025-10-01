<?php
//  insert data user
$usr = $_POST['username'];
$psw = $_POST['password'];
$cpsw = $_POST['confirmpassword'];
$fname = $_POST['fullname'];
$role = $_POST['role'];

// check value password and confirm password
if($psw == $cpsw){
  // hash password
  $psw = password_hash($psw, PASSWORD_DEFAULT);
  // $psw = md5($cpsw); // for demo purpose, do not use this in production
}else{
  die("Error: Password and Confirm Password do not match.");
}
//create connection
include "../<config>/conn_db.php";
$sql = "SELECT username FROM users WHERE username='$usr'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  die("Error: Username already exists. Please choose a different username.<br><a href='form_register.php'>Back to Register</a>");
}

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