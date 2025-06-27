<?php
session_start();
require_once 'db.php';

// Determină sezonul curent
function getCurrentSeason() {
    $month = date('n');
    if ($month >= 3 && $month <= 5) return 'primavara';
    if ($month >= 6 && $month <= 8) return 'vara';
    if ($month >= 9 && $month <= 11) return 'toamna';
    return 'iarna';
}

$currentSeason = getCurrentSeason();

try {
    $query = "SELECT * FROM shoes 
              WHERE (season = :season OR season = 'all')
              ORDER BY rating DESC LIMIT 6";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':season' => $currentSeason]);
    $seasonalRecommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error getting seasonal recommendations: " . $e->getMessage());
    $seasonalRecommendations = [];
}

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
  <link rel="stylesheet" href="Styling/style.css" />
  <link rel="stylesheet" href="Styling/home.css" />
</head>
<body>
  <header>
    <h1>SmartFoot</h1>
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

  <section class="welcome-section">
    <h2>Bine ați venit la SmartFoot!</h2>
    <p>Descoperiți colecția noastră de încălțăminte și primiți recomandări personalizate.</p>
  </section>

  <section class="seasonal-recommendations">
    <h2>Recomandări pentru <?php echo $currentSeason; ?></h2>
    <?php if (!empty($seasonalRecommendations)): ?>
      <div class="recommendations-grid">
        <?php foreach ($seasonalRecommendations as $shoe): ?>
          <div class="shoe-card">
            <?php if (!empty($shoe['image_url'])): ?>
              <img src="<?php echo htmlspecialchars($shoe['image_url']); ?>" alt="<?php echo htmlspecialchars($shoe['name']); ?>">
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($shoe['name']); ?></h3>
            <p class="shoe-description"><?php echo htmlspecialchars($shoe['description']); ?></p>
            <p class="shoe-price">Preț: <?php echo number_format($shoe['price'], 2); ?> RON</p>
            <p class="shoe-brand">Brand: <?php echo htmlspecialchars($shoe['brand']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>Ne pare rău, nu sunt disponibile recomandări pentru acest sezon momentan.</p>
    <?php endif; ?>
  </section>

  <?php include 'includes/footer.php'; ?>
  
</body>
</html>
