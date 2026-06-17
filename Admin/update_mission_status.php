<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__.'/includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['statut'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

$id = $data['id'];
$statut = $data['statut'];

try {
    if ($statut === 'Supprimée') {
        $stmt = $pdo->prepare("DELETE FROM demandes_mission WHERE id = ?");
        $stmt->execute([$id]);
    } else {
        $stmt = $pdo->prepare("UPDATE demandes_mission SET statut = ? WHERE id = ?");
        $stmt->execute([$statut, $id]);
    }
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
