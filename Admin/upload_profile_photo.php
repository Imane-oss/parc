<?php
session_start();
require_once __DIR__.'/includes/db_connection.php';

header('Content-Type: application/json');

// Récupérer l'user_id — fallback sur le premier admin si session absente
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    // Fallback: prendre le premier utilisateur admin
    $stmtAdmin = $pdo->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    $adminRow = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
    $userId = $adminRow['id'] ?? null;
}

if (!$userId) {
    echo json_encode(['success' => false, 'error' => 'Aucun admin trouvé']);
    exit;
}

if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Aucun fichier uploadé ou erreur d\'upload']);
    exit;
}

$file = $_FILES['photo'];
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'error' => 'Format non supporté (Uniquement JPG, PNG, GIF, WEBP)']);
    exit;
}

if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
    echo json_encode(['success' => false, 'error' => 'Fichier trop volumineux (Max 5Mo)']);
    exit;
}

$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
$uploadDir = __DIR__ . '/uploads/profiles/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$destination = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $destination)) {
    $publicPath = 'uploads/profiles/' . $filename;

    // Update the database
    $stmt = $pdo->prepare('UPDATE users SET profile_photo = ? WHERE id = ?');
    if ($stmt->execute([$publicPath, $userId])) {
        echo json_encode(['success' => true, 'path' => $publicPath]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur de mise à jour de la base de données']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'enregistrement du fichier']);
}
