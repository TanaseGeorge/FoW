<?php

session_start();

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Ajutor - FoW</title>
    <link rel="stylesheet" href="Styling/info.css">
    <link rel="stylesheet" href="Styling/style.css">
</head>
<body>
<header>
    <h3>SmartFoot</h3>
    <nav>
      <a href="index.php" class="active">Home</a>
      <a href="catalog.php">Catalog</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="recommendations.php">Recomandari</a>
        <a href="statistics.php">Statistici</a>
        <a href="logout.php">Deconectare</a>
      <?php else: ?>
        <a href="login_form.php">Autentificare</a>
        <a href="register.php">Înregistrare</a>
      <?php endif; ?>
    </nav>
  </header>
    <div class="info-container">
        <h1>Ajutor & Întrebări Frecvente</h1>

        <h2>Cum îmi creez un cont?</h2>
        <p>Accesează pagina de <a href="register.php">înregistrare</a>, completează câmpurile și apasă pe butonul „Înregistrare”.</p>

        <h2>Cum primesc recomandări?</h2>
        <p>După autentificare, accesează secțiunea „Recomandări” și completează criteriile dorite: sezon, stil, brand, ocazie etc.</p>
        
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
