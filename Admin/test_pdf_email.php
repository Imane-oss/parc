<?php
require_once 'includes/db_connection.php';
require_once 'includes/PdfGenerator.php';
require_once 'includes/EmailSender.php';

// Get first mission from DB
$stmt = $pdo->query("SELECT * FROM demandes_mission ORDER BY id DESC LIMIT 1");
$mission = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mission) {
    die("No missions found in DB.");
}

echo "Testing with mission: " . $mission['nom'] . " " . ($mission['prenom'] ?? '') . "\n";
echo "Email: " . ($mission['email'] ?? 'EMPTY') . "\n";

// 1. Test PDF generation
try {
    $pdfData = [
        'nom'          => $mission['nom'],
        'prenom'       => $mission['prenom'] ?? '',
        'matricule'    => $mission['matricule'] ?? '',
        'role'         => $mission['direction'] ?? '',
        'service'      => 'PARC AUTO',
        'direction'    => $mission['direction'] ?? '',
        'accompagne'   => 'Non',
        'transport'    => 'Véhicule de service',
        'vehicle_plate'=> $mission['matricule'] ?? '',
        'destination'  => $mission['destination'],
        'motif'        => $mission['motif_mission'],
        'date_depart'  => $mission['date_depart'],
        'date_retour'  => $mission['date_retour'],
    ];

    $pdfPath = PdfGenerator::generateMissionPdf($pdfData);
    echo "✅ PDF generated at: " . $pdfPath . "\n";
    echo "✅ PDF file size: " . filesize($pdfPath) . " bytes\n";
} catch (Exception $e) {
    die("❌ PDF generation failed: " . $e->getMessage());
}

// 2. Test email with PDF attachment
try {
    EmailSender::sendMissionAcceptedEmail('zianiiman060@gmail.com', $mission['nom'], $pdfPath);
    echo "✅ Email with PDF sent successfully to zianiiman060@gmail.com!\n";
} catch (Exception $e) {
    echo "❌ Email failed: " . $e->getMessage() . "\n";
}

// 3. Cleanup
if (file_exists($pdfPath)) {
    unlink($pdfPath);
    echo "✅ Temp PDF cleaned up.\n";
}
