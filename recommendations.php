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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartFoot - Recomandări</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="recommendations.css">
</head>
<body>
    <header>
        <h1>SmartFoot</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="catalog.php">Catalog</a>
            <a href="recommendations.php" class="active">Sugestii</a>
            <a href="statistics.php">Statistici</a>
            <a href="logout.php">Deconectare</a>
        </nav>
    </header>

    <div class="container">
        <h2>Găsește încălțămintea perfectă</h2>
        
        <form id="recommendationForm">
            <div class="form-group">
                <label for="occasion">Ocazie:</label>
                <select name="occasion" id="occasion" required>
                    <option value="">Selectează o ocazie</option>
                    <option value="business">Întâlnire de afaceri/Interviu</option>
                    <option value="casual">Casual/Zilnic</option>
                    <option value="formal">Eveniment formal</option>
                    <option value="sport">Sport/Activități fizice</option>
                    <option value="party">Petrecere/Ieșire în oraș</option>
                </select>
            </div>

            <div class="form-group">
                <label for="season">Sezon:</label>
                <select name="season" id="season" required>
                    <option value="">Selectează un sezon</option>
                    <option value="spring">Primăvară</option>
                    <option value="summer">Vară</option>
                    <option value="autumn">Toamnă</option>
                    <option value="winter">Iarnă</option>
                </select>
            </div>

            <div class="form-group">
                <label for="style">Stil preferat:</label>
                <select name="style" id="style" required>
                    <option value="">Selectează un stil</option>
                    <option value="classic">Clasic</option>
                    <option value="modern">Modern</option>
                    <option value="sporty">Sport</option>
                    <option value="elegant">Elegant</option>
                    <option value="casual">Casual</option>
                </select>
            </div>

            <div class="form-group">
                <label for="brand_preference">Mărci preferate:</label>
                <input type="text" name="brand_preference" id="brand_preference" placeholder="Introdu mărcile preferate (opțional)">
            </div>

            <div class="form-group">
                <label for="email">Email pentru recomandări:</label>
                <input type="email" name="email" id="email" required placeholder="Introdu adresa de email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>">
            </div>

            <button type="submit">Obține recomandări</button>
        </form>

        <div id="recommendations" class="recommendations-container"></div>
    </div>

    <footer>
        <p>&copy; 2025 SmartFoot. Toate drepturile rezervate.</p>
    </footer>

    <script>
    document.getElementById('recommendationForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const recommendationsContainer = document.getElementById('recommendations');
        
        try {
            const response = await fetch('get_recommendations.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                let html = '<h3>Recomandările tale:</h3>';
                data.recommendations.forEach(rec => {
                    html += `
                        <div class="recommendation-item">
                            <h4>${rec.title}</h4>
                            <p>${rec.description}</p>
                            <div class="details">
                                <p><strong>Tip:</strong> ${rec.type}</p>
                                <p><strong>Preț:</strong> ${rec.priceRange}</p>
                                <p>${rec.brand}</p>
                            </div>
                        </div>
                    `;
                });
                recommendationsContainer.innerHTML = html;
                
                if (data.emailSent) {
                    alert('Recomandările au fost trimise și pe email!');
                }
            } else {
                recommendationsContainer.innerHTML = `
                    <div class="error-message">
                        ${data.message}
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error:', error);
            recommendationsContainer.innerHTML = `
                <div class="error-message">
                    A apărut o eroare. Vă rugăm încercați din nou.
                </div>
            `;
        }
    });
    </script>

    <style>
    .recommendation-item {
        background: #f8f9fa;
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 4px;
        border-left: 4px solid #3498db;
    }

    .recommendation-item h4 {
        color: #2c3e50;
        margin: 0 0 0.5rem 0;
    }

    .recommendation-item .details {
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #dee2e6;
    }

    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 1rem;
        border-radius: 4px;
        margin-top: 1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: bold;
    }

    .form-group select,
    .form-group input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    button {
        background: #3498db;
        color: white;
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
    }

    button:hover {
        background: #2980b9;
    }
    </style>
</body>
</html> 