<?php
$host = "localhost";      
$user = "root";           
$pass = "";               
$db   = "data_teritori";      

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}
?>
