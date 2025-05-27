<?php
session_start();
header('Content-Type: application/json');

// Database connection
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Vă rugăm să vă autentificați pentru a primi recomandări personalizate'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $occasion = $_POST['occasion'] ?? '';
    $season = $_POST['season'] ?? '';
    $style = $_POST['style'] ?? '';
    $brand_preference = $_POST['brand_preference'] ?? '';
    $email = $_POST['email'] ?? '';
    
    if (empty($occasion) || empty($season) || empty($style) || empty($email)) {
        echo json_encode([
            'success' => false,
            'message' => 'Vă rugăm să completați toate câmpurile obligatorii'
        ]);
        exit;
    }

    function getShoeRecommendations($occasion, $season, $style) {
        global $pdo;

        $sql = "SELECT name, brand, price, description, season, style, occasion
                FROM shoes
                WHERE season = :season
                  AND style = :style
                  AND occasion = :occasion
                LIMIT 5";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':season' => $season,
            ':style' => $style,
            ':occasion' => $occasion
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get recommendations from database
    try {
        $recommendations = getShoeRecommendations($occasion, $season, $style);
        
        if (empty($recommendations)) {
            echo json_encode([
                'success' => false,
                'message' => 'Ne pare rău, nu am găsit recomandări pentru criteriile selectate. Vă rugăm să încercați alte criterii.'
            ]);
            exit;
        }
        
        // Format recommendations for display
        $formattedRecommendations = array_map(function($shoe) {
            return [
                'title' => $shoe['name'],
                'description' => $shoe['description'],
                'type' => ucfirst($shoe['occasion']),
                'priceRange' => '$' . number_format($shoe['price'], 2),
                'brand' => 'Recommended brand: ' . $shoe['brand']
            ];
        }, $recommendations);
        
        // Save statistics
        saveStatistics($_SESSION['user_id'], $occasion, $season, $style);
        
        // Send email
        $emailSent = sendRecommendationEmail($email, $formattedRecommendations);
        
        // Generate statistics files
        $statsDir = "statistics";
        if (!file_exists($statsDir)) {
            mkdir($statsDir, 0777, true);
        }
        
        $timestamp = date('Y-m-d_H-i-s');
        
        // HTML
        $htmlStats = "<html><body><h2>Statistici Recomandări</h2><p>Generate la: " . date('Y-m-d H:i:s') . "</p>";
        $htmlStats .= "<p>Ocazie: $occasion</p><p>Sezon: $season</p><p>Stil: $style</p>";
        $htmlStats .= "<h3>Recomandări:</h3><ul>";
        foreach ($formattedRecommendations as $rec) {
            $htmlStats .= "<li>{$rec['title']} - {$rec['brand']}</li>";
        }
        $htmlStats .= "</ul></body></html>";
        file_put_contents("statistics/stats_$timestamp.html", $htmlStats);
        
        // CSV
        $csvStats = "Timestamp,Occasion,Season,Style,Recommendations\n";
        $csvStats .= date('Y-m-d H:i:s') . ",$occasion,$season,$style,\"" . 
                     implode(', ', array_column($formattedRecommendations, 'title')) . "\"\n";
        file_put_contents("statistics/stats_$timestamp.csv", $csvStats);
        
        // XML
        $xmlStats = "<?xml version='1.0' encoding='UTF-8'?>\n<statistics>\n";
        $xmlStats .= "\t<timestamp>" . date('Y-m-d H:i:s') . "</timestamp>\n";
        $xmlStats .= "\t<occasion>$occasion</occasion>\n\t<season>$season</season>\n\t<style>$style</style>\n";
        $xmlStats .= "\t<recommendations>\n";
        foreach ($formattedRecommendations as $rec) {
            $xmlStats .= "\t\t<recommendation>\n";
            $xmlStats .= "\t\t\t<title>{$rec['title']}</title>\n";
            $xmlStats .= "\t\t\t<brand>{$rec['brand']}</brand>\n";
            $xmlStats .= "\t\t</recommendation>\n";
        }
        $xmlStats .= "\t</recommendations>\n</statistics>";
        file_put_contents("statistics/stats_$timestamp.xml", $xmlStats);
        
        echo json_encode([
            'success' => true,
            'recommendations' => $formattedRecommendations,
            'emailSent' => $emailSent
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'A apărut o problemă la obținerea recomandărilor. Vă rugăm să încercați mai târziu.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Metodă de cerere invalidă'
    ]);
}

// Function to send email
function sendRecommendationEmail($email, $recommendations) {
    $subject = "Recomandările tale de încălțăminte";
    
    $message = "Salut!\n\nIată recomandările tale personalizate de încălțăminte:\n\n";
    
    foreach($recommendations as $rec) {
        $message .= "-------------------\n";
        $message .= $rec['title'] . "\n";
        $message .= $rec['description'] . "\n";
        $message .= "Tip: " . $rec['type'] . "\n";
        $message .= "Preț: " . $rec['priceRange'] . "\n";
        if(isset($rec['brand'])) {
            $message .= $rec['brand'] . "\n";
        }
        $message .= "-------------------\n\n";
    }
    
    $headers = "From: noreply@smartfoot.com";
    
    return mail($email, $subject, $message, $headers);
}
?>
