<?php
// register.php

session_start();

if (isset($_SESSION['user_id'])) {
  header('Location: todo.php');
  exit();
}

require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $stmt = $conn->prepare('SELECT id FROM users WHERE username = :username');
  $stmt->bindParam(':username', $username);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $message = 'O nome de usuário já está em uso. Por favor, escolha outro.';
  } else {
    $stmt = $conn->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);

    try {
      $stmt->execute();
      $_SESSION['user_id'] = $conn->lastInsertId();
      header('Location: todo.php');
      exit();
    } catch (PDOException $e) {
      $message = 'Erro ao cadastrar usuário. Por favor, tente novamente.';
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Cadastro - To Do List</title>
  <style>
    body {
      background-color: #f4f4f4;
      font-family: Arial, sans-serif;
    }

    h1 {
      text-align: center;
      color: #333;
    }

    form {
      width: 300px;
      margin: 0 auto;
      background-color: #fff;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    label {
      display: block;
      margin-bottom: 10px;
      color: #333;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }

    input[type="submit"] {
      width: 100%;
      padding: 10px;
      background-color: #333;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 10px;
    }

    input[type="submit"]:hover {
      background-color: #555;
    }

    a {
      display: block;
      text-align: center;
      margin-top: 10px;
      color: #333;
    }
  </style>
</head>
<body>
  <h1>Cadastro</h1>
  <?php if ($message): ?>
    <p><?php echo $message; ?></p>
  <?php endif; ?>
  <form method="POST">
    <label for="username">Usuário:</label>
    <input type="text" name="username" required>
    <br>
    <label for="password">Senha:</label>
    <input type="password" name="password" required>
    <br>
    <input type="submit" value="Cadastrar">
  </form>
  <a href="index.php">Login</a>
</body>
</html>

