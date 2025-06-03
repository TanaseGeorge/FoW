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
unset($row); // good practice

// Generare în funcție de format
switch ($format) {
    case 'csv':
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="statistics.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Ocazie', 'Sezon', 'Stil', 'Dată', 'Sugestie']);
        foreach ($rows as $row) {
            fputcsv($output, [
                $row['occasion'], $row['season'], $row['style'],
                $row['created_at'], $row['suggestion']
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
            $entry->addChild('occasion', $row['occasion']);
            $entry->addChild('season', $row['season']);
            $entry->addChild('style', $row['style']);
            $entry->addChild('created_at', $row['created_at']);
            $entry->addChild('suggestion', $row['suggestion']);
        }
        echo $xml->asXML();
        break;

    case 'html':
        echo "<!DOCTYPE html><html lang='ro'><head><meta charset='UTF-8'><title>Statistici HTML</title></head><body>";
        echo "<h2>Statistici exportate</h2>";
        echo "<table border='1' cellpadding='8' cellspacing='0'>";
        echo "<thead><tr><th>Ocazie</th><th>Sezon</th><th>Stil</th><th>Dată</th><th>Sugestie</th></tr></thead><tbody>";
        foreach ($rows as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['occasion']) . "</td>";
            echo "<td>" . htmlspecialchars($row['season']) . "</td>";
            echo "<td>" . htmlspecialchars($row['style']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "<td>" . htmlspecialchars($row['suggestion']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table></body></html>";
        break;

    default:
        echo "Format invalid.";
        break;
}
