<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Recomandări</title>
    <link rel="stylesheet" href="Styling/recommendations.css">
    <link rel="stylesheet" href="Styling/style.css">
    <script defer src="recommendations.js"></script>
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

    <div class="recom-container">
        <h1>Primește recomandări de încălțăminte</h1>

        <form id="recommendationForm">
            <label for="season">Sezon:</label>
            <select name="season" id="season" required>
                <option value="vara">Vara</option>
                <option value="iarna">Iarna</option>
                <option value="toamna">Toamna</option>
                <option value="primavara">Primăvara</option>
            </select>

            <label for="occasion">Ocazie:</label>
            <select name="occasion" id="occasion" required>
                <option value="casual">Casual</option>
                <option value="sport">Sport</option>
                <option value="elegant">Elegant</option>
            </select>

            <label for="style">Stil:</label>
            <select name="style" id="style" required>
                <option value="sneakers">Sneakers</option>
                <option value="sandale">Sandale</option>
                <option value="papuci">Papuci</option>
                <option value="cizme">Cizme</option>
            </select>

            <label for="custom_email">Trimite pe email (opțional):</label>
            <input type="email" name="custom_email" id="custom_email" placeholder="ex: tu@email.com">

            <button type="submit">Generează recomandări</button>
        </form>

        <div id="recommendations" class="recommendations"></div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>