<?php
session_start();

// Verificăm dacă utilizatorul este autentificat
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit;
}

require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartFoot - Statistici</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="recommendations.css">
</head>
<body>
    <header>
        <h1>SmartFoot</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="catalog.php">Catalog</a>
            <a href="recommendations.php">Sugestii</a>
            <a href="statistics.php" class="active">Statistici</a>
            <a href="logout.php">Deconectare</a>
        </nav>
    </header>

    <div class="container">
        <h1>Recommendation Statistics</h1>
        
        <div class="stats-container">
            <h2>Recent Recommendations</h2>
            <div class="stats-format-links">
                <p>Download statistics in different formats:</p>
                <ul>
                    <?php
                    $files = glob("statistics/stats_*.{html,csv,xml}", GLOB_BRACE);
                    $latestFiles = array_slice(array_reverse($files), 0, 3);
                    
                    foreach ($latestFiles as $file) {
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                        $filename = basename($file);
                        echo "<li><a href='$file' target='_blank'>Latest $ext format</a></li>";
                    }
                    ?>
                </ul>
            </div>

            <div class="recent-stats">
                <?php
                if (!empty($files)) {
                    $latestHtml = array_filter($files, function($f) { return pathinfo($f, PATHINFO_EXTENSION) == 'html'; });
                    if (!empty($latestHtml)) {
                        $latestHtml = reset($latestHtml);
                        echo "<div class='stats-preview'>";
                        echo "<h3>Latest Statistics Preview</h3>";
                        include($latestHtml);
                        echo "</div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 SmartFoot. Toate drepturile rezervate.</p>
    </footer>

    <style>
        .stats-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stats-format-links {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .stats-format-links ul {
            list-style: none;
            padding: 0;
            margin: 10px 0;
        }
        
        .stats-format-links li {
            margin: 5px 0;
        }
        
        .stats-format-links a {
            color: #3498db;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            background: #fff;
            display: inline-block;
            border: 1px solid #3498db;
        }
        
        .stats-format-links a:hover {
            background: #3498db;
            color: white;
        }
        
        .stats-preview {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
        }
    </style>
</body>
</html> 