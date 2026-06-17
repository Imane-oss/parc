<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__.'/includes/db_connection.php';
require_once __DIR__.'/includes/PdfGenerator.php';
require_once __DIR__.'/includes/EmailSender.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Ensure user is logged in as Admin (optional security check depending on your auth setup)
if (!isset($_SESSION['user_id'])) {
    // For now, let's allow it or you can uncomment to enforce auth
    // echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    // exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Mission ID is required']);
    exit;
}

$missionId = $data['id'];
$vehicleName = $data['vehicle_name'] ?? 'Véhicule de service';
$vehiclePlate = $data['vehicle_plate'] ?? '';

try {
    // 1. Fetch the request from DB
    $stmt = $pdo->prepare("SELECT * FROM demandes_mission WHERE id = ?");
    $stmt->execute([$missionId]);
    $mission = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mission) {
        echo json_encode(['success' => false, 'message' => 'Mission not found']);
        exit;
    }

    // 2. Prepare data for PDF
    $pdfData = [
        'nom' => $mission['nom'],
        'prenom' => $mission['prenom'],
        'matricule' => $mission['matricule'] ?? '',
        'role' => $mission['direction'] ?? '', // Or 'fonction' if you have it
        'service' => 'PARC AUTO', // Or dynamic if available
        'direction' => $mission['direction'],
        'accompagne' => 'Non', // Update if you have this in DB
        'transport' => $vehicleName,
        'vehicle_plate' => $vehiclePlate,
        'destination' => $mission['destination'],
        'motif' => $mission['motif_mission'],
        'date_depart' => $mission['date_depart'],
        'date_retour' => $mission['date_retour'],
    ];

    // 3. Generate PDF
    $pdfPath = PdfGenerator::generateMissionPdf($pdfData);

    // 4. Update DB Status
    $updateStmt = $pdo->prepare("UPDATE demandes_mission SET statut = 'Approuvée', vehicle_name = ?, vehicle_plate = ? WHERE id = ?");
    $updateStmt->execute([$vehicleName, $vehiclePlate, $missionId]);

    // 5. Cleanup Temporary PDF
    if (file_exists($pdfPath)) {
        unlink($pdfPath);
    }

    echo json_encode(['success' => true, 'message' => 'Mission accepted. User interface updated.']);

} catch (Exception $e) {
    error_log("Error in accept_mission.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
