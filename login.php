<?php
session_start();

// Verificăm dacă este o cerere POST (din formular) sau GET (acces direct)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login_form.php');
    exit;
}

require 'db.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['logged_in'] = true;

    header('Location: index.php');
    exit;
} else {
    $_SESSION['error'] = "Email sau parolă incorectă.";
    header('Location: login_form.php');
    exit;
}
?>
