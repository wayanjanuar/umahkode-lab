<?php
// login_vuln.php (vulnerable intentionally)
require 'db.php'; // returns $conn (mysqli)

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$query = "SELECT id, username FROM users WHERE username='$username' AND password='$password'";
// vulnerable: interpolated user input
$res = $conn->query($query);

if($res && $res->num_rows>0){
  echo "Login successful";
}else{
  echo "Login failed";
}
