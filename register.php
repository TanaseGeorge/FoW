<?php
require 'db.php';

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($password !== $confirm) {
  die("Parolele nu coincid.");
}

$hashed = password_hash($password, PASSWORD_BCRYPT);

try {
  $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
  $stmt->execute([$name, $email, $hashed]);
  header("Location: login.html");
  exit;
} catch (PDOException $e) {
  if ($e->getCode() == '23505') {
    die("Email deja folosit.");
  }
  die("Eroare la înregistrare: " . $e->getMessage());
}
?>
