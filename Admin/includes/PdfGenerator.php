<?php

require_once __DIR__ . '/../vendor/autoload.php';

use setasign\Fpdi\Fpdi;

class PdfGenerator {
    
    public static function generateMissionPdf($data) {
        $pdf = new Fpdi('P', 'pt', 'A4');
        
        $templatePath = __DIR__ . '/../Demande ordre mission .pdf';
        
        if (!file_exists($templatePath)) {
            $templatePath = __DIR__ . '/../Demande ordre mission conbiné.pdf'; // fallback
        }

        $pageCount = $pdf->setSourceFile($templatePath);
        $tplId = $pdf->importPage(1);
        
        $pdf->AddPage();
        $pdf->useTemplate($tplId, 0, 0, null, null, true);
        
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->SetTextColor(0, 23, 55); // Dark blue text
        
        // FPDF coordinates based on pdf_layout.json (top-left origin, in points)
        // Adjusting X offsets slightly to place text *after* the colons.
        
        // Nom & Prenom
        $pdf->SetXY(160, 182);
        $pdf->Write(0, utf8_decode($data['nom']));
        $pdf->SetXY(350, 182);
        $pdf->Write(0, utf8_decode($data['prenom'] ?? ''));

        // Matricule & Fonction (Role)
        $pdf->SetXY(160, 220);
        $pdf->Write(0, utf8_decode($data['matricule'] ?? ''));
        $pdf->SetXY(350, 220);
        $pdf->Write(0, utf8_decode($data['role'] ?? ''));

        // Service
        $pdf->SetXY(160, 255);
        $pdf->Write(0, utf8_decode($data['service'] ?? 'PARC AUTO'));

        // Direction
        $pdf->SetXY(160, 289);
        $pdf->Write(0, utf8_decode($data['direction'] ?? 'DIRECTION GENERALE'));

        // Accompagné
        $pdf->SetXY(160, 324);
        $pdf->Write(0, utf8_decode($data['accompagne'] ?? 'Non'));

        // Moyen de transport
        $pdf->SetXY(200, 358);
        $pdf->Write(0, utf8_decode($data['transport'] ?? 'Véhicule de service'));

        // Matricule véhicule
        $pdf->SetXY(180, 393);
        $pdf->Write(0, utf8_decode($data['vehicle_plate'] ?? ''));
        
        // Lieu de deplacement (Destination)
        $pdf->SetXY(180, 420); // approximated Y
        $pdf->Write(0, utf8_decode($data['destination'] ?? ''));

        // Objet (Motif)
        $pdf->SetXY(160, 468);
        $pdf->Write(0, utf8_decode($data['motif'] ?? ''));

        // Date et heure de départ
        $pdf->SetXY(220, 507);
        $pdf->Write(0, utf8_decode($data['date_depart'] ?? ''));

        // Date et heure de retour
        $pdf->SetXY(220, 542);
        $pdf->Write(0, utf8_decode($data['date_retour'] ?? ''));

        $tmpDir = __DIR__ . '/../tmp';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $filename = 'mission_' . uniqid() . '.pdf';
        $outputPath = $tmpDir . '/' . $filename;
        
        $pdf->Output('F', $outputPath);
        
        return $outputPath;
    }
}
