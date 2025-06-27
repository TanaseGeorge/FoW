<?php
/**
 * API Router centralizat pentru aplicația FoW
 * Toate cererile AJAX vin aici și sunt rutate către funcții specifice
 */

session_start();
require_once 'db.php';

// Headers JSON pentru toate răspunsurile
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * Funcție helper pentru răspunsuri JSON
 */
function jsonResponse($success, $data = null, $message = '', $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8'),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Verifică dacă utilizatorul este autentificat
 */
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(false, null, 'Autentificare necesară', 401);
    }
}

/**
 * Sanitizează input-ul pentru prevenirea XSS
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Obține acțiunea și metoda HTTP
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {
        
        // === AUTENTIFICARE ===
        case 'login':
            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                $email = sanitize($input['email'] ?? '');
                $password = $input['password'] ?? '';
                
                if (empty($email) || empty($password)) {
                    jsonResponse(false, null, 'Email și parola sunt obligatorii', 400);
                }
                
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    
                    jsonResponse(true, [
                        'user' => [
                            'id' => $user['id'],
                            'name' => sanitize($user['name']),
                            'email' => sanitize($user['email'])
                        ]
                    ], 'Autentificare reușită');
                } else {
                    jsonResponse(false, null, 'Email sau parolă incorectă', 401);
                }
            } else {
                jsonResponse(false, null, 'Metodă nepermisă', 405);
            }
            break;
        
        // === ÎNREGISTRARE ===
        case 'register':
            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                // Extrage și sanitizează datele
                $name = sanitize($input['name'] ?? '');
                $email = sanitize($input['email'] ?? '');
                $password = $input['password'] ?? '';
                $confirmPassword = $input['confirm_password'] ?? '';
                
                // Validare server-side
                if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
                    jsonResponse(false, null, 'Toate câmpurile sunt obligatorii', 400);
                }
                
                // Validare nume
                if (strlen($name) < 2) {
                    jsonResponse(false, null, 'Numele trebuie să aibă minim 2 caractere', 400);
                }
                
                // Validare email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    jsonResponse(false, null, 'Adresa de email nu este validă', 400);
                }
                
                // Validare parolă
                if (strlen($password) < 6) {
                    jsonResponse(false, null, 'Parola trebuie să aibă minim 6 caractere', 400);
                }
                
                // Verifică dacă parolele se potrivesc
                if ($password !== $confirmPassword) {
                    jsonResponse(false, null, 'Parolele nu se potrivesc', 400);
                }
                
                try {
                    // Verifică dacă email-ul există deja
                    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                    $checkStmt->execute([$email]);
                    
                    if ($checkStmt->fetch()) {
                        jsonResponse(false, null, 'Un cont cu acest email există deja', 409);
                    }
                    
                    // Hash-uiește parola
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Inserează utilizatorul nou
                    $insertStmt = $pdo->prepare("
                        INSERT INTO users (name, email, password, isadmin) 
                        VALUES (?, ?, ?, 0)
                    ");
                    
                    $insertStmt->execute([$name, $email, $hashedPassword]);
                    
                    // Obține ID-ul utilizatorului nou creat
                    $userId = $pdo->lastInsertId();
                    
                    // Log activitatea
                    error_log("New user registered: ID=$userId, Email=$email");
                    
                    jsonResponse(true, [
                        'user_id' => $userId,
                        'message' => 'Cont creat cu succes'
                    ], 'Înregistrare reușită');
                    
                } catch (PDOException $e) {
                    error_log("Database error in register: " . $e->getMessage());
                    
                    // Verifică dacă e eroare de duplicat email (fallback)
                    if (strpos($e->getMessage(), 'duplicate') !== false || strpos($e->getMessage(), 'unique') !== false) {
                        jsonResponse(false, null, 'Un cont cu acest email există deja', 409);
                    } else {
                        jsonResponse(false, null, 'Eroare la crearea contului. Încercați din nou.', 500);
                    }
                }
                
            } else {
                jsonResponse(false, null, 'Metodă nepermisă', 405);
            }
            break;

        case 'logout':
            if ($method === 'POST') {
                session_destroy();
                jsonResponse(true, null, 'Deconectare reușită');
            } else {
                jsonResponse(false, null, 'Metodă nepermisă', 405);
            }
            break;
        
        // === CATALOG ===
        case 'catalog':
            if ($method === 'GET') {
                $page = intval($_GET['page'] ?? 1);
                $limit = intval($_GET['limit'] ?? 12);
                $offset = ($page - 1) * $limit;
                
                // Filtre exacte ca în versiunea veche
                $search = sanitize($_GET['search'] ?? '');
                $occasion = sanitize($_GET['occasion'] ?? '');
                $season = sanitize($_GET['season'] ?? '');
                $style = sanitize($_GET['style'] ?? '');
                $priceMin = $_GET['price_min'] ?? '';
                $priceMax = $_GET['price_max'] ?? '';
                
                $whereClause = [];
                $params = [];
                
                // Căutare în nume și descriere
                if ($search) {
                    $whereClause[] = "(name ILIKE ? OR description ILIKE ? OR brand ILIKE ?)";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                }
                
                // Filtru ocazie
                if ($occasion) {
                    $whereClause[] = "occasion = ?";
                    $params[] = $occasion;
                }
                
                // Filtru sezon
                if ($season) {
                    $whereClause[] = "season = ?";
                    $params[] = $season;
                }
                
                // Filtru stil
                if ($style) {
                    $whereClause[] = "style = ?";
                    $params[] = $style;
                }
                
                // Filtru preț minim (ca în versiunea veche)
                if ($priceMin !== '' && is_numeric($priceMin)) {
                    $whereClause[] = "price >= ?";
                    $params[] = floatval($priceMin);
                }
                
                // Filtru preț maxim (ca în versiunea veche)
                if ($priceMax !== '' && is_numeric($priceMax)) {
                    $whereClause[] = "price <= ?";
                    $params[] = floatval($priceMax);
                }
                
                $where = $whereClause ? 'WHERE ' . implode(' AND ', $whereClause) : '';
                
                // Query principal
                $sql = "SELECT * FROM shoes $where ORDER BY rating DESC LIMIT ? OFFSET ?";
                $params[] = $limit;
                $params[] = $offset;
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $shoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Total pentru paginare
                $countSql = "SELECT COUNT(*) FROM shoes $where";
                $countStmt = $pdo->prepare($countSql);
                $countStmt->execute(array_slice($params, 0, -2));
                $total = $countStmt->fetchColumn();
                
                // Sanitizează datele pentru output
                foreach ($shoes as &$shoe) {
                    $shoe['name'] = sanitize($shoe['name']);
                    $shoe['description'] = sanitize($shoe['description']);
                    $shoe['brand'] = sanitize($shoe['brand']);
                }
                
                jsonResponse(true, [
                    'products' => $shoes,
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => intval($total),
                        'pages' => ceil($total / $limit)
                    ]
                ]);
            } else {
                jsonResponse(false, null, 'Metodă nepermisă', 405);
            }
            break;
        
        // === RECOMANDĂRI ===
        case 'recommendations':
            requireAuth();
            
            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                $occasion = sanitize($input['occasion'] ?? '');
                $season = sanitize($input['season'] ?? '');
                $style = sanitize($input['style'] ?? '');
                
                if (empty($occasion) || empty($season) || empty($style)) {
                    jsonResponse(false, null, 'Toate câmpurile sunt obligatorii', 400);
                }
                
                // Folosește funcția existentă din db.php
                $recommendations = getShoeRecommendations($occasion, $season, $style);
                
                if (empty($recommendations)) {
                    jsonResponse(false, null, 'Nu s-au găsit recomandări pentru selecția dumneavoastră', 404);
                }
                
                // Salvează în statistici
                saveStatistics($_SESSION['user_id'], $occasion, $season, $style);
                
                // Sanitizează output
                foreach ($recommendations as &$rec) {
                    $rec['name'] = sanitize($rec['name']);
                    $rec['description'] = sanitize($rec['description']);
                    $rec['brand'] = sanitize($rec['brand']);
                }
                
                jsonResponse(true, [
                    'recommendations' => $recommendations
                ], 'Recomandări generate cu succes');
                
            } else {
                jsonResponse(false, null, 'Metodă nepermisă', 405);
            }
            break;
        
        // === STATISTICI ===
        case 'statistics':
            requireAuth();
            
            if ($method === 'GET') {
                $stmt = $pdo->prepare(
                    "SELECT occasion, season, style, created_at 
                     FROM statistics 
                     WHERE user_id = ? 
                     ORDER BY created_at DESC"
                );
                $stmt->execute([$_SESSION['user_id']]);
                $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Sanitizează datele
                foreach ($stats as &$stat) {
                    $stat['occasion'] = sanitize($stat['occasion']);
                    $stat['season'] = sanitize($stat['season']);
                    $stat['style'] = sanitize($stat['style']);
                }
                
                jsonResponse(true, ['statistics' => $stats]);
            } else {
                jsonResponse(false, null, 'Metodă nepermisă', 405);
            }
            break;
        
        // === EXPORT ===
        case 'export':
            requireAuth();
            
            if ($method === 'GET') {
                $format = $_GET['format'] ?? 'json';
                
                $stmt = $pdo->prepare(
                    "SELECT occasion, season, style, created_at 
                     FROM statistics 
                     WHERE user_id = ? 
                     ORDER BY created_at DESC"
                );
                $stmt->execute([$_SESSION['user_id']]);
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                switch ($format) {
                    case 'json':
                        jsonResponse(true, [
                            'statistics' => $data,
                            'exported_at' => date('Y-m-d H:i:s'),
                            'format' => 'json'
                        ], 'Export JSON generat cu succes');
                        break;
                        
                    case 'csv':
                        header('Content-Type: text/csv');
                        header('Content-Disposition: attachment; filename="statistics.csv"');
                        
                        $output = fopen('php://output', 'w');
                        fputcsv($output, ['Ocazie', 'Sezon', 'Stil', 'Data']);
                        foreach ($data as $row) {
                            fputcsv($output, [
                                $row['occasion'],
                                $row['season'], 
                                $row['style'],
                                $row['created_at']
                            ]);
                        }
                        fclose($output);
                        exit;
                        
                    default:
                        jsonResponse(false, null, 'Format de export invalid', 400);
                }
            } else {
                jsonResponse(false, null, 'Metodă nepermisă', 405);
            }
            break;
        
        // === ADMIN ===
        case 'admin':
            requireAuth();
            
            if ($method === 'GET') {
                $entity = $_GET['entity'] ?? '';
                
                switch ($entity) {
                    case 'users':
                        $stmt = $pdo->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC");
                        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($users as &$user) {
                            $user['name'] = sanitize($user['name']);
                            $user['email'] = sanitize($user['email']);
                        }
                        
                        jsonResponse(true, ['users' => $users]);
                        break;
                        
                    case 'products':
                        $stmt = $pdo->query("SELECT * FROM shoes ORDER BY created_at DESC LIMIT 50");
                        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($products as &$product) {
                            $product['name'] = sanitize($product['name']);
                            $product['description'] = sanitize($product['description']);
                            $product['brand'] = sanitize($product['brand']);
                        }
                        
                        jsonResponse(true, ['products' => $products]);
                        break;
                        
                    case 'stats':
                        $stmt = $pdo->query("
                            SELECT 
                                COUNT(*) as total_recommendations,
                                COUNT(DISTINCT user_id) as active_users,
                                occasion,
                                COUNT(*) as count
                            FROM statistics 
                            GROUP BY occasion
                            ORDER BY count DESC
                        ");
                        $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        jsonResponse(true, ['admin_stats' => $stats]);
                        break;
                        
                    default:
                        jsonResponse(false, null, 'Entitatea nu există', 404);
                }
            } else {
                jsonResponse(false, null, 'Metodă nepermisă', 405);
            }
            break;
        
        default:
            jsonResponse(false, null, 'Acțiunea nu există', 404);
    }
    
} catch (PDOException $e) {
    error_log("Database error in API: " . $e->getMessage());
    jsonResponse(false, null, 'Eroare de bază de date', 500);
} catch (Exception $e) {
    error_log("General error in API: " . $e->getMessage());
    jsonResponse(false, null, 'Eroare server', 500);
}
?>