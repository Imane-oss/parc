<?php
// Connexion à la base de données
$host = "localhost";
$user = "root";
$password = "";
$dbname = "parc_auto";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>Erreur de connexion: " . $e->getMessage() . "</div>");
}

// 1. Récupérer le total des demandes
$stmtTotal = $pdo->query("SELECT COUNT(*) FROM demandes_mission");
$totalDemandes = $stmtTotal->fetchColumn();

// 2. Vérifier si la colonne statut existe avant d'exécuter les requêtes de statut
$hasStatutColumn = (bool) $pdo->query("SHOW COLUMNS FROM demandes_mission LIKE 'statut'")->fetch(PDO::FETCH_ASSOC);

if ($hasStatutColumn) {
    $stmtApprouve = $pdo->query("SELECT COUNT(*) FROM demandes_mission WHERE statut = 'Approuvée'");
    $totalApprouve = $stmtApprouve->fetchColumn();

    $stmtAttente = $pdo->query("SELECT COUNT(*) FROM demandes_mission WHERE statut = 'En attente'");
    $totalAttente = $stmtAttente->fetchColumn();
} else {
    $totalApprouve = 0;
    $totalAttente = 0;
}

// 4. Récupérer toutes les demandes pour le tableau
$stmtList = $pdo->query("SELECT * FROM demandes_mission ORDER BY created_at DESC");
$demandes = $stmtList->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>parc automobile - Historique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #0056b3;
            --bg-light: #f8fafc;
            --text-dark: #1e293b;
            --header-height: 85px;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
        }

        .top-header {
            width: 100%;
            height: var(--header-height);
            position: fixed;
            top: 0;
            left: 0;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align