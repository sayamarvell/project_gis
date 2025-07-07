<?php
session_start();

// Ganti ini sesuai data kamu
$valid_user = "admin";
$valid_pass = "1234567890";

if ($_POST['username'] === $valid_user && $_POST['password'] === $valid_pass) {
  $_SESSION['admin_logged_in'] = true;
  header("Location: admin.php");
} else {
  echo "<script>alert('Login gagal!'); window.location='login.php';</script>";
}
