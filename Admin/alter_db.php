<?php
$pdo = new PDO('mysql:host=localhost;dbname=parc_auto', 'root', '');
try {
    $pdo->exec('ALTER TABLE demandes_mission ADD COLUMN vehicle_name VARCHAR(255) NULL, ADD COLUMN vehicle_plate VARCHAR(255) NULL');
    echo "Success\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
