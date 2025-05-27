<?php
session_start();

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SmartFoot – Home</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <header>
    <h1>SmartFoot</h1>
    <nav>
      <a href="index.php" class="active">Home</a>
      <a href="catalog.php">Catalog</a>
      <a href="recommendations.php">Sugestii</a>
      <a href="statistics.php">Statistici</a>
      <a href="logout.php">Deconectare</a>
    </nav>
  </header>

  <main>
    <section class="hero">
      <h2>Găsește încălțămintea potrivită pentru orice ocazie</h2>
      <p>De la interviuri la campionate de sumo – te acoperim cu stil.</p>
      <button onclick="scrollToRecom()">Vezi recomandările</button>
    </section>

    <section class="recommendations" id="recom">
      <h3>Recomandări de sezon</h3>
      <div class="cards">
        <div class="card">
          <img src="assets/boots.jpg" alt="Cizme pictate" />
          <h4>Primăvară nocturnă</h4>
          <p>Se apropie primăvara. Pentru plimbări nocturne, cizmele pictate sunt alegerea ideală!</p>
        </div>
        <div class="card">
          <img src="assets/heels.jpg" alt="Pantofi cu toc de inox" />
          <h4>Ceremonii dansante</h4>
          <p>Pentru ceremonii dansante, poartă pantofi cu toc de inox și o togă argintie asortată.</p>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 SmartFoot. Toate drepturile rezervate.</p>
  </footer>

  <script src="script.js"></script>
</body>
</html> 