<?php
session_start();
require 'db.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['season'], $_POST['occasion'], $_POST['style'])) {
    echo json_encode(['success' => false, 'message' => 'Date incomplete sau utilizator neautentificat.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$season = $_POST['season'];
$occasion = $_POST['occasion'];
$style = $_POST['style'];

// Preia emailul personalizat din formular (dacă e valid)
$user_email = filter_var($_POST['custom_email'] ?? '', FILTER_VALIDATE_EMAIL);

function generateSuggestion($occasion, $season, $style) {
    $text = "Ținând cont de selecția ta – ocazie <strong>$occasion</strong>, sezon <strong>$season</strong>, stil <strong>$style</strong> – îți oferim o recomandare inspirată:";
    if ($occasion === 'sport') $text .= " Alege modele comode, cu amortizare bună.";
    if ($occasion === 'casual') $text .= " Pantofii comozi și versatili sunt perfecți.";
    if ($occasion === 'elegant') $text .= " Recomandăm materiale premium și accente metalice.";
    if ($season === 'vara') $text .= " Preferă materiale ușoare și aerisite.";
    if ($season === 'iarna') $text .= " Talpa groasă și interiorul căptușit sunt importante.";
    if ($style === 'sneakers') $text .= " Sneakers-ii sunt potriviți pentru multe contexte.";
    if ($style === 'sandale') $text .= " Sandalele sunt ideale pentru vară și plimbări.";
    if ($style === 'papuci') $text .= " Papucii sunt o alegere relaxată și rapidă.";
    if ($style === 'cizme') $text .= " Cizmele oferă protecție și stil în anotimpurile reci.";
    return $text;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM shoes WHERE season = :season AND occasion = :occasion AND style = :style ORDER BY rating DESC LIMIT 5");
    $stmt->execute([':season' => $season, ':occasion' => $occasion, ':style' => $style]);
    $shoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$shoes) {
        echo json_encode(['success' => false, 'message' => 'Nu am găsit recomandări.']);
        exit;
    }

    $pdo->prepare("INSERT INTO statistics (user_id, occasion, season, style) VALUES (:user_id, :occasion, :season, :style)")
        ->execute([':user_id' => $user_id, ':occasion' => $occasion, ':season' => $season, ':style' => $style]);

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

    if (!$user_email) {
        echo json_encode(['success' => true, 'recommendations' => $formatted, 'note' => 'Emailul nu a fost trimis (adresă lipsă sau invalidă).']);
        exit;
    }

    // Trimitere email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply.smartfoot@gmail.com'; // <- schimbă
        $mail->Password = 'jpnkmtxdxmguiemw';  // <- schimbă
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('noreply.smartfoot@gmail.com', 'SmartFoot');
        $mail->addAddress($user_email);
        $mail->Subject = 'Recomandările tale de încălțăminte';

        $body = "Salut!\n\nAi primit următoarele recomandări:\n\n";
        foreach ($formatted as $rec) {
            $body .= "• {$rec['title']} ({$rec['priceRange']}) - {$rec['brand']}\n";
            $body .= html_entity_decode(strip_tags($rec['description'])) . "\n\n";
        }

        $mail->isHTML(true); 
        $mail->CharSet = 'UTF-8';
        $mail->Body = nl2br(htmlentities($body));
        $mail->send();
    } catch (Exception $e) {
        error_log("Eroare email: " . $mail->ErrorInfo);
    }

    echo json_encode(['success' => true, 'recommendations' => $formatted]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Eroare server: ' . $e->getMessage()]);
}