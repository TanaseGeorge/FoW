<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['format'])) {
    http_response_code(400);
    echo "Neautorizat sau format lipsă.";
    exit;
}

$user_id = $_SESSION['user_id'];
$format = $_POST['format'];

$stmt = $pdo->prepare("SELECT occasion, season, style, created_at FROM statistics WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute(['user_id' => $user_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($rows)) {
    echo "Nu există statistici de exportat.";
    exit;
}

// Funcție pentru sugestie detaliată
function generateSuggestion($occasion, $season, $style) {
    $occasion = strtolower($occasion);
    $season = strtolower($season);
    $style = strtolower($style);

    $text = "Ocazie: $occasion, sezon: $season, stil: $style – ";

    if ($occasion === 'sport') {
        $text .= "alege încălțăminte cu susținere și materiale respirabile.";
    } elseif ($occasion === 'casual') {
        $text .= "mizează pe confort și versatilitate pentru ținutele zilnice.";
    } elseif ($occasion === 'elegant') {
        $text .= "pantofii cu accente rafinate sunt alegerea ideală.";
    }

    if ($season === 'vara') {
        $text .= " Evită materiale grele, mergi pe opțiuni aerisite.";
    } elseif ($season === 'iarna') {
        $text .= " Alege talpă antiderapantă și izolație bună.";
    } elseif ($season === 'toamna') {
        $text .= " Texturi mate și culori neutre sunt ideale.";
    } elseif ($season === 'primavara') {
        $text .= " Poți experimenta cu modele colorate și ușoare.";
    }

    if ($style === 'sneakers') {
        $text .= " Sneakers-ii sunt potriviți aproape oricând.";
    } elseif ($style === 'sandale') {
        $text .= " Sandalele oferă confort și stil vara.";
    } elseif ($style === 'papuci') {
        $text .= " Papucii sunt practici și relaxați.";
    } elseif ($style === 'cizme') {
        $text .= " Cizmele combină protecția cu eleganța.";
    }

    return $text;
}

// Adaugă sugestiile în fiecare rând
foreach ($rows as &$row) {
    $row['suggestion'] = generateSuggestion($row['occasion'], $row['season'], $row['style']);
}
unset($row);

// Generare în funcție de format
switch ($format) {
    case 'csv':
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="statistics.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Ocazie', 'Sezon', 'Stil', 'Dată', 'Sugestie']);
        foreach ($rows as $row) {
            fputcsv($output, [
                htmlspecialchars($row['occasion'], ENT_QUOTES, 'UTF-8'), 
                htmlspecialchars($row['season'], ENT_QUOTES, 'UTF-8'), 
                htmlspecialchars($row['style'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8'), 
                htmlspecialchars($row['suggestion'], ENT_QUOTES, 'UTF-8')
            ]);
        }
        fclose($output);
        break;

    case 'xml':
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="statistics.xml"');

        $xml = new SimpleXMLElement('<statistics/>');
        foreach ($rows as $row) {
            $entry = $xml->addChild('entry');
            $entry->addChild('occasion', htmlspecialchars($row['occasion'], ENT_QUOTES, 'UTF-8'));
            $entry->addChild('season', htmlspecialchars($row['season'], ENT_QUOTES, 'UTF-8'));
            $entry->addChild('style', htmlspecialchars($row['style'], ENT_QUOTES, 'UTF-8'));
            $entry->addChild('created_at', htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8'));
            $entry->addChild('suggestion', htmlspecialchars($row['suggestion'], ENT_QUOTES, 'UTF-8'));
        }
        echo $xml->asXML();
        break;

    case 'json':
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="statistics.json"');
        
        // Sanitizează datele pentru JSON
        $jsonData = [];
        foreach ($rows as $row) {
            $jsonData[] = [
                'occasion' => htmlspecialchars($row['occasion'], ENT_QUOTES, 'UTF-8'),
                'season' => htmlspecialchars($row['season'], ENT_QUOTES, 'UTF-8'),
                'style' => htmlspecialchars($row['style'], ENT_QUOTES, 'UTF-8'),
                'created_at' => htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8'),
                'suggestion' => htmlspecialchars($row['suggestion'], ENT_QUOTES, 'UTF-8')
            ];
        }
        
        echo json_encode([
            'format' => 'json',
            'exported_at' => date('Y-m-d H:i:s'),
            'total_records' => count($jsonData),
            'user_id' => $user_id,
            'statistics' => $jsonData
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;

    case 'html':
        echo "<!DOCTYPE html><html lang='ro'><head><meta charset='UTF-8'><title>Statistici HTML</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
            th { background-color: #f2f2f2; font-weight: bold; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .suggestion { max-width: 300px; word-wrap: break-word; }
        </style></head><body>";
        echo "<h2>Statistici exportate - " . date('Y-m-d H:i:s') . "</h2>";
        echo "<p><strong>Numărul total de înregistrări:</strong> " . count($rows) . "</p>";
        echo "<table>";
        echo "<thead><tr><th>Ocazie</th><th>Sezon</th><th>Stil</th><th>Dată</th><th>Sugestie</th></tr></thead><tbody>";
        foreach ($rows as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['occasion'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($row['season'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($row['style'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td class='suggestion'>" . htmlspecialchars($row['suggestion'], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "<hr><p><em>Exportat din aplicația ShoeReco</em></p>";
        echo "</body></html>";
        break;

    default:
        http_response_code(400);
        echo "Format invalid. Formate acceptate: csv, xml, json, html";
        break;
}
?>