<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['season'], $_POST['occasion'], $_POST['style'])) {
    echo json_encode(['success' => false, 'message' => 'Date incomplete sau utilizator neautentificat.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$season = $_POST['season'];
$occasion = $_POST['occasion'];
$style = $_POST['style'];

function generateSuggestion($occasion, $season, $style) {
    $occasion = strtolower($occasion);
    $season = strtolower($season);
    $style = strtolower($style);

    $text = "Ținând cont de selecția ta – ocazie <strong>$occasion</strong>, sezon <strong>$season</strong>, stil <strong>$style</strong> – îți oferim o recomandare inspirată:";

    if ($occasion === 'sport') {
        $text .= " alege încălțăminte cu susținere bună pentru gleznă și materiale respirabile, mai ales dacă practici activități intense.";
    } elseif ($occasion === 'casual') {
        $text .= " modele ușoare, comode, în culori neutre sau pastel sunt perfecte pentru ținute zilnice.";
    } elseif ($occasion === 'elegant') {
        $text .= " pantofii cu detalii metalice sau texturi lucioase adaugă rafinament, ideali pentru evenimente și întâlniri formale.";
    }

    if ($season === 'vara') {
        $text .= " Evită materialele grele și optează pentru perechi cât mai deschise la culoare și aerisite.";
    } elseif ($season === 'iarna') {
        $text .= " Alege încălțăminte căptușită, cu talpă antiderapantă – stilul contează, dar confortul termic este esențial.";
    } elseif ($season === 'toamna') {
        $text .= " Texturile mate și culorile pământii (bej, maro, oliv) merg de minune cu paltoane și trenciuri.";
    } elseif ($season === 'primavara') {
        $text .= " Experimentează combinații pastel și materiale ușoare – sezonul renașterii cere și creativitate în garderobă.";
    }

    if ($style === 'sneakers') {
        $text .= " Sneakers-ii sunt versatili – poți merge cu ei la birou, la plimbare sau chiar la o ieșire informală.";
    } elseif ($style === 'sandale') {
        $text .= " Sandalele sunt ideale pentru zilele toride – caută modele cu susținere și curele reglabile pentru confort.";
    } elseif ($style === 'papuci') {
        $text .= " Papucii pot fi chic și practici – o alegere relaxată, mai ales dacă mizezi pe simplitate și design modern.";
    } elseif ($style === 'cizme') {
        $text .= " Cizmele sunt ideale pentru protecție și stil – scurte sau lungi, caută modele care oferă și eleganță, și funcționalitate.";
    }

    return $text;
}

try {
    $stmt = $pdo->prepare("
        SELECT * FROM shoes
        WHERE season = :season AND occasion = :occasion AND style = :style
        ORDER BY rating DESC
        LIMIT 5
    ");
    $stmt->execute([
        ':season' => $season,
        ':occasion' => $occasion,
        ':style' => $style
    ]);
    $shoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$shoes) {
        echo json_encode(['success' => false, 'message' => 'Nu am găsit recomandări.']);
        exit;
    }

    $insert = $pdo->prepare("
        INSERT INTO statistics (user_id, occasion, season, style)
        VALUES (:user_id, :occasion, :season, :style)
    ");
    $insert->execute([
        ':user_id' => $user_id,
        ':occasion' => $occasion,
        ':season' => $season,
        ':style' => $style
    ]);

    $formatted = array_map(function ($s) use ($occasion, $season, $style) {
        return [
            'title' => $s['name'],
            'description' => $s['description'] . "<br><em>" . generateSuggestion($occasion, $season, $style) . "</em>",
            'type' => $s['occasion'],
            'priceRange' => $s['price'] . ' €',
            'brand' => $s['brand'],
            'image_url' => $s['image_url']
        ];
    }, $shoes);

    echo json_encode([
        'success' => true,
        'recommendations' => $formatted
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Eroare server: ' . $e->getMessage()
    ]);
}
