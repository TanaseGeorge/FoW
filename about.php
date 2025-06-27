<?php

session_start();

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Despre - FoW</title>
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
        <h1>Despre FoW</h1>
        <p><strong>FoW</strong> este o platformă inteligentă care oferă recomandări de încălțăminte personalizate, pe baza stilului, sezonului, ocaziei și preferințelor tale.</p>

        <h2>Caracteristici</h2>
        <ul>
            <li>Recomandări inteligente de produse</li>
            <li>Filtrare avansată după sezon, stil, brand și preț</li>
            <li>Gestionare administrativă a produselor și utilizatorilor</li>
            <li>Export de statistici în formate HTML/CSV/XML</li>
        </ul>

        <p>Proiectul este dezvoltat in cadrul materiei Tehnologii Web.</p>
    </div>


    <?php include 'includes/footer.php'; ?>
</body>

</html>
