<?php
session_start();
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']); // ștergem mesajul după ce l-am citit
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Autentificare</title>
  <link rel="stylesheet" href="auth.css"/>
</head>
<body>

  <div class="auth-container">
    <h2>Autentificare</h2>
    <?php if ($error): ?>
    <div class="message error">
      <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    <form action="login.php" method="POST">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Parolă" required />
      <button type="submit">Autentificare</button>
      <p>Nu ai cont? <a href="register.php">Înregistrează-te</a></p>
    </form>
  </div>


  <style>
    .message {
      margin-bottom: 1rem;
      padding: 1rem;
      border-radius: 4px;
      text-align: center;
    }
    
    .message.error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    .auth-container {
      max-width: 400px;
      margin: 2rem auto;
      padding: 2rem;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .auth-container form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    
    .auth-container input {
      padding: 0.8rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 1rem;
    }
    
    .auth-container button {
      padding: 1rem;
      background: #3498db;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    
    .auth-container button:hover {
      background: #2980b9;
    }
  </style>
</body>
</html> 