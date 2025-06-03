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
    <title>Recomandări Încălțăminte</title>
    <link rel="stylesheet" href="recommendations.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div><strong>ShoeReco</strong></div>
        <nav>
            <a href="index.php">Acasă</a>
            <a href="catalog.php">Catalog</a>
            <a href="statistics.php">Statistici</a>
            <a href="logout.php">Deconectare</a>
        </nav>
    </header>

    <div class="container">
        <h1>Primește recomandări personalizate</h1>

        <form id="recommendationForm">
            <div class="form-group">
                <label for="occasion">Ocazie</label>
                <select name="occasion" id="occasion" required>
                    <option value="">-- Selectează --</option>
                    <option value="sport">Sport</option>
                    <option value="casual">Casual</option>
                    <option value="elegant">Elegant</option>
                </select>
            </div>

            <div class="form-group">
                <label for="season">Sezon</label>
                <select name="season" id="season" required>
                    <option value="">-- Selectează --</option>
                    <option value="primavara">Primăvară</option>
                    <option value="vara">Vară</option>
                    <option value="toamna">Toamnă</option>
                    <option value="iarna">Iarnă</option>
                </select>
            </div>

            <div class="form-group">
                <label for="style">Stil</label>
                <select name="style" id="style" required>
                    <option value="">-- Selectează --</option>
                    <option value="sneakers">Sneakers</option>
                    <option value="cizme">Cizme</option>
                    <option value="papuci">Papuci</option>
                    <option value="sandale">Sandale</option>
                </select>
            </div>

            <button type="submit">Generează recomandări</button>
        </form>

        <div class="recommendations-container" id="recommendations">
            <!-- Aici apar rezultatele -->
        </div>
    </div>

    <script src="recommendations.js"></script>
</body>
</html>
