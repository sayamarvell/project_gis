<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
  header("Location: admin.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Admin</title>
  <style>
    body {
      font-family: sans-serif;
      background: #b71c1c;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    form {
      background: #fff;
      color: #000;
      padding: 20px 30px;
      border-radius: 12px;
      width: 300px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    input {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    button {
      margin-top: 15px;
      background: #b71c1c;
      color: white;
      border: none;
      padding: 10px;
      width: 100%;
      border-radius: 6px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <form method="POST" action="auth.php">
    <h2>Login Admin</h2>
    <input type="text" name="username" placeholder="Username" required />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit">Login</button>
  </form>
</body>
</html>
