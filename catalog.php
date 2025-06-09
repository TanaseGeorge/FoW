<?php
session_start();
require_once 'db.php';

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$filters = [
  'search' => $_GET['search'] ?? '',
  'occasion' => $_GET['occasion'] ?? '',
  'season' => $_GET['season'] ?? '',
  'style' => $_GET['style'] ?? '',
  'price_min' => $_GET['price_min'] ?? '',
  'price_max' => $_GET['price_max'] ?? ''
];

$where = [];
$params = [];

if ($filters['search']) {
  $where[] = "(name ILIKE :search OR brand ILIKE :search OR description ILIKE :search)";
  $params[':search'] = '%' . $filters['search'] . '%';
}
if ($filters['occasion']) {
  $where[] = "occasion = :occasion";
  $params[':occasion'] = $filters['occasion'];
}
if ($filters['season']) {
  $where[] = "season = :season";
  $params[':season'] = $filters['season'];
}
if ($filters['style']) {
  $where[] = "style = :style";
  $params[':style'] = $filters['style'];
}
if (is_numeric($filters['price_min'])) {
  $where[] = "price >= :price_min";
  $params[':price_min'] = $filters['price_min'];
}
if (is_numeric($filters['price_max'])) {
  $where[] = "price <= :price_max";
  $params[':price_max'] = $filters['price_max'];
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';


// Preluăm produsele din baza de date
try {
  $stmt = $pdo->prepare("SELECT * FROM shoes $whereClause ORDER BY id DESC");
  $stmt->execute($params);
  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
  $error = "Eroare la preluarea produselor: " . $e->getMessage();
}

// Array cu imagini alternative pentru fiecare tip de încălțăminte
$defaultImages = [
    'pantofi' => 'https://images.unsplash.com/photo-1449505278894-297fdb3edbc1',
    'sandale' => 'https://images.unsplash.com/photo-1562273138-f46be4ebdf33',
    'cizme' => 'https://images.unsplash.com/photo-1542280756-74b2f55e73ab',
    'papuci' => 'https://images.unsplash.com/photo-1545231027-637d2f6210f8',
    'sneakers' => 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2'
];

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartFoot - Catalog</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-name {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        .product-brand {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-description {
            color: #444;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .product-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        .product-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .product-rating {
            display: flex;
            align-items: center;
            color: #f39c12;
            font-weight: bold;
        }

        .product-rating::before {
            content: "★";
            margin-right: 4px;
        }

        .product-meta {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }

        .product-meta span {
            background: #f8f9fa;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.85rem;
            color: #666;
            text-transform: capitalize;
        }

        .error {
            color: #e74c3c;
            text-align: center;
            padding: 1rem;
            background: #fdf0ed;
            border-radius: 4px;
            margin: 1rem;
        }

        .filters {
            max-width: 1000px;
            margin: 2rem auto 0;
            padding: 1rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }
        .filters form {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }
        .filters input,
        .filters select {
            padding: 0.5rem;
            width: 100%;
        }
        .filters button {
            grid-column: 1 / -1;
            padding: 0.6rem;
            background: #2196f3;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>SmartFoot</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="catalog.php" class="active">Catalog</a>
            <a href="recommendations.php">Recomandări</a>
            <a href="statistics.php">Statistici</a>
            <a href="logout.php">Deconectare</a>
        </nav>
    </header>

    <main>
        
    <section class="filters">
          <form method="GET">
              <input type="text" name="search" placeholder="Căutare..." value="<?= htmlspecialchars($filters['search']) ?>">
              <select name="occasion">
                  <option value="">-- Ocazie --</option>
                  <option value="casual" <?= $filters['occasion']==='casual' ? 'selected' : '' ?>>Casual</option>
                  <option value="sport" <?= $filters['occasion']==='sport' ? 'selected' : '' ?>>Sport</option>
                  <option value="elegant" <?= $filters['occasion']==='elegant' ? 'selected' : '' ?>>Elegant</option>
              </select>
              <select name="season">
                  <option value="">-- Sezon --</option>
                  <option value="vara" <?= $filters['season']==='vara' ? 'selected' : '' ?>>Vara</option>
                  <option value="iarna" <?= $filters['season']==='iarna' ? 'selected' : '' ?>>Iarna</option>
                  <option value="toamna" <?= $filters['season']==='toamna' ? 'selected' : '' ?>>Toamna</option>
                  <option value="primavara" <?= $filters['season']==='primavara' ? 'selected' : '' ?>>Primăvara</option>
              </select>
              <select name="style">
                  <option value="">-- Stil --</option>
                  <option value="sneakers" <?= $filters['style']==='sneakers' ? 'selected' : '' ?>>Sneakers</option>
                  <option value="sandale" <?= $filters['style']==='sandale' ? 'selected' : '' ?>>Sandale</option>
                  <option value="papuci" <?= $filters['style']==='papuci' ? 'selected' : '' ?>>Papuci</option>
                  <option value="cizme" <?= $filters['style']==='cizme' ? 'selected' : '' ?>>Cizme</option>
              </select>
              <input type="number" name="price_min" placeholder="Preț minim" value="<?= htmlspecialchars($filters['price_min']) ?>">
              <input type="number" name="price_max" placeholder="Preț maxim" value="<?= htmlspecialchars($filters['price_max']) ?>">
              <button type="submit">Filtrează</button>
          </form>
    </section>
        <section class="product-grid">
                <?php if (isset($error)): ?>
                        <p class="error"><?php echo $error; ?></p>
                <?php elseif (empty($products)): ?>
                        <p class="error">Nu am găsit produse care să corespundă filtrului.</p>
                <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php
                        // Folosim imaginea implicită pentru tipul de încălțăminte dacă imaginea principală nu se încarcă
                        $defaultImage = $defaultImages[$product['style']] ?? $defaultImages['pantofi'];
                        ?>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             class="product-image"
                             onerror="this.src='<?php echo $defaultImage; ?>'">
                        <div class="product-info">
                            <h2 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h2>
                            <div class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></div>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="product-meta">
                                <span><?php echo htmlspecialchars($product['occasion']); ?></span>
                                <span><?php echo htmlspecialchars($product['season']); ?></span>
                                <span><?php echo htmlspecialchars($product['style']); ?></span>
                            </div>
                            <div class="product-details">
                                <div class="product-price"><?php echo number_format($product['price'], 2); ?> RON</div>
                                <div class="product-rating"><?php echo number_format($product['rating'], 1); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 SmartFoot. Toate drepturile rezervate.</p>
    </footer>
</body>
</html> 