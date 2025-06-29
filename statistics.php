<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT occasion, season, style, created_at FROM statistics WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute(['user_id' => $user_id]);
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Statistici</title>
    <link rel="stylesheet" href="Styling/statistics.css">
    <link rel="stylesheet" href="Styling/style.css">
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

    <div class="stats-container">
        <h1>Statistici ale recomandărilor tale</h1>

        <?php if (empty($stats)): ?>
            <p>Nu există statistici disponibile pentru contul tău.</p>
        <?php else: ?>
            <table class="stats-table">
                <thead>
                    <tr>
                        <th>Ocazie</th>
                        <th>Sezon</th>
                        <th>Stil</th>
                        <th>Dată</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['occasion']) ?></td>
                            <td><?= htmlspecialchars($row['season']) ?></td>
                            <td><?= htmlspecialchars($row['style']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <form action="export_statistics.php" method="POST" style="margin-top: 2rem;">
                <button type="submit" name="format" value="csv">Descarcă CSV</button>
                <button type="submit" name="format" value="xml">Descarcă XML</button>
                <button type="submit" name="format" value="json">Descarcă JSON</button>
                <button type="submit" name="format" value="html" formaction="export_statistics.php" formtarget="_blank">Vezi HTML separat</button>
            </form>
        <?php endif; ?>
    </div>
    <?php include 'includes/footer.php'; ?>                        
</body>
</html>
