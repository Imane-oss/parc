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

// Récupérer toutes les demandes pour le tableau
$stmtList = $pdo->query("SELECT * FROM demandes_mission ORDER BY created_at DESC");
$demandes = $stmtList->fetchAll(PDO::FETCH_ASSOC);

// Statistiques
$hasStatutColumn = (bool) $pdo->query("SHOW COLUMNS FROM demandes_mission LIKE 'statut'")->fetch(PDO::FETCH_ASSOC);

if ($hasStatutColumn) {
    $stmtApprouve = $pdo->query("SELECT COUNT(*) FROM demandes_mission WHERE statut = 'Approuvée'");
    $totalApprouve = $stmtApprouve->fetchColumn();

    $stmtAttente = $pdo->query("SELECT COUNT(*) FROM demandes_mission WHERE statut = 'En attente' OR statut IS NULL");
    $totalAttente = $stmtAttente->fetchColumn();
} else {
    $totalApprouve = 0;
    $totalAttente = count($demandes);
}
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
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            z-index: 1020;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-container img {
            height: 55px;
            width: auto;
        }

        .brand-text {
            font-size: 13pt;
            font-weight: bold;
            color: var(--primary-blue);
            line-height: 1.3;
        }

        .brand-text span {
            color: #e67e22;
            font-size: 11pt;
        }

        .btn-deconnexion {
            background-color: #dc2626;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-weight: 600;
            text-decoration: none;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: calc(100vh - var(--header-height));
            position: fixed;
            top: var(--header-height);
            left: 0;
            background-color: #ffffff;
            border-right: 1px solid #e2e8f0;
            padding-top: 20px;
            z-index: 1010;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-item a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
        }

        .sidebar-item a:hover, .sidebar-item.active a {
            color: var(--primary-blue);
            background-color: #f1f5f9;
            border-left: 4px solid var(--primary-blue);
        }

        .main-wrapper {
            margin-top: var(--header-height);
            margin-left: var(--sidebar-width);
            padding: 40px;
            min-height: calc(100vh - var(--header-height));
            display: flex;
            flex-direction: column;
        }

        .card-stats {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .table-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status-approuvee {
            background-color: #ecfdf5;
            color: #10b981;
            border: 1px solid #d1fae5;
        }
        .status-attente {
            background-color: #fffbeb;
            color: #f59e0b;
            border: 1px solid #fef3c7;
        }
    </style>
</head>
<body>

    <header class="top-header">
        <div class="logo-container">
            <img src="../static/images/srm_pic.png" alt="Logo">
            <div class="brand-text">
                Société Régionale Multiservices<br>
                <span>Béni Mellal - Khénifra S.A</span>
            </div>
        </div>
        <a href="logout.php" class="btn-deconnexion"><i class="fa-solid fa-right-from-bracket"></i></a>
    </header>

    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li class="sidebar-item active">
                <a href="afficher_demandes.php">
                    <i class="fa-solid fa-clock-history"></i>
                    <span>Historique</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="nouvelle_demande.php">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Nouvelle Demande</span>
                </a>
            </li>
        </ul>
    </aside>

    <div class="main-wrapper">
        <div class="mb-4">
            <h2 class="fw-bold m-0">HISTORIQUE DES DEMANDES</h2>
            <p class="text-muted small m-0">Consultez l'état d'avancement de vos demandes d'ordre de mission</p>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card-stats">
                    <div class="stat-icon" style="background-color: #ecfdf5; color: #10b981;">
                        <i class="fa-solid fa-check-circle"></i>
                    </div>
                    <div>
                        <h4 class="m-0 fw-bold"><?= $totalApprouve ?></h4>
                        <span class="text-muted small uppercase">Demandes Acceptées</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-stats">
                    <div class="stat-icon" style="background-color: #fffbeb; color: #f59e0b;">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <h4 class="m-0 fw-bold"><?= $totalAttente ?></h4>
                        <span class="text-muted small uppercase">Demandes en Attente</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4 text-muted small uppercase">Date de création</th>
                            <th class="py-3 px-4 text-muted small uppercase">Collaborateur</th>
                            <th class="py-3 px-4 text-muted small uppercase">Destination</th>
                            <th class="py-3 px-4 text-muted small uppercase">Période</th>
                            <th class="py-3 px-4 text-muted small uppercase">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($demandes) > 0): ?>
                            <?php foreach ($demandes as $demande): ?>
                                <tr>
                                    <td class="py-3 px-4">
                                        <strong><?= date('d/m/Y', strtotime($demande['created_at'])) ?></strong><br>
                                        <small class="text-muted"><?= date('H:i', strtotime($demande['created_at'])) ?></small>
                                    </td>
                                    <td class="py-3 px-4">
                                        <strong><?= htmlspecialchars($demande['nom'] . ' ' . $demande['prenom']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($demande['direction']) ?></small>
                                    </td>
                                    <td class="py-3 px-4">
                                        <strong><?= htmlspecialchars($demande['destination']) ?></strong><br>
                                        <small class="text-muted">"<?= htmlspecialchars($demande['motif_mission']) ?>"</small>
                                    </td>
                                    <td class="py-3 px-4">
                                        Du <strong><?= date('d/m/Y', strtotime($demande['date_depart'])) ?></strong><br>
                                        Au <strong><?= date('d/m/Y', strtotime($demande['date_retour'])) ?></strong>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php if (($demande['statut'] ?? '') === 'Approuvée'): ?>
                                            <span class="badge-status status-approuvee">
                                                <i class="fa-solid fa-check"></i> Votre demande a été acceptée
                                            </span>
                                        <?php else: ?>
                                            <span class="badge-status status-attente">
                                                <i class="fa-solid fa-hourglass-half"></i> En attente de validation
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open mb-2" style="font-size: 2rem;"></i><br>
                                    Aucune demande n'a été trouvée.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
