<?php
$host = "localhost";
$port = "5432";
$dbname = "FoW";
$user = "postgres";
$password = "postgres"; // Înlocuiește cu parola ta

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verifică dacă tabelele există
    $tables = [
        'users' => "CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        'shoes' => "CREATE TABLE IF NOT EXISTS shoes (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            occasion VARCHAR(50) NOT NULL,
            season VARCHAR(50) NOT NULL,
            style VARCHAR(50) NOT NULL,
            brand VARCHAR(100),
            price DECIMAL(10,2),
            rating DECIMAL(3,2),
            image_url TEXT
        )",
        'statistics' => "CREATE TABLE IF NOT EXISTS statistics (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL,
            occasion VARCHAR(50) NOT NULL,
            season VARCHAR(50) NOT NULL,
            style VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ];
    
    foreach ($tables as $table => $query) {
        $pdo->exec($query);
    }
    
} catch(PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Ne pare rău, a apărut o problemă cu conexiunea la baza de date. Vă rugăm să încercați mai târziu.");
}

// Funcție pentru a obține recomandări din baza de date
function getShoeRecommendations($occasion, $season, $style) {
    global $pdo;
    
    try {
        // Mai întâi încercăm să găsim potriviri exacte
        $query = "SELECT * FROM shoes 
                  WHERE occasion = :occasion 
                  AND (season = :season OR season = 'all')
                  AND (style = :style OR style = 'versatile')
                  ORDER BY rating DESC LIMIT 3";
                  
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':occasion' => $occasion,
            ':season' => $season,
            ':style' => $style
        ]);
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Dacă nu găsim rezultate, încercăm să relaxăm criteriile
        if (empty($results)) {
            $query = "SELECT * FROM shoes 
                      WHERE (occasion = :occasion OR occasion = 'versatile')
                      AND (season = :season OR season = 'all')
                      ORDER BY rating DESC LIMIT 3";
                      
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':occasion' => $occasion,
                ':season' => $season
            ]);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $results;
        
    } catch (PDOException $e) {
        error_log("Error in getShoeRecommendations: " . $e->getMessage());
        throw new Exception("A apărut o eroare la obținerea recomandărilor.");
    }
}

// Funcție pentru a salva statisticile
function saveStatistics($userId, $occasion, $season, $style) {
    global $pdo;
    
    try {
        $query = "INSERT INTO statistics (user_id, occasion, season, style, created_at) 
                  VALUES (:user_id, :occasion, :season, :style, CURRENT_TIMESTAMP)";
                  
        $stmt = $pdo->prepare($query);
        return $stmt->execute([
            ':user_id' => $userId,
            ':occasion' => $occasion,
            ':season' => $season,
            ':style' => $style
        ]);
    } catch (PDOException $e) {
        error_log("Error in saveStatistics: " . $e->getMessage());
        // Nu aruncăm excepție aici pentru că salvarea statisticilor nu este critică
        return false;
    }
}
?>
