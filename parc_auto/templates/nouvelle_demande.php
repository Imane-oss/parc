<?php
// Connexion à la base de données
$host = "localhost";
$user = "root";
$password = "";
$dbname = "parc_auto";

$message = "";
$demande_id = null;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $message = "<div class='alert alert-danger'>Erreur de connexion: " . $e->getMessage() . "</div>";
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {
    $nom = htmlspecialchars($_POST['nom'] ?? '');
    $prenom = htmlspecialchars($_POST['prenom'] ?? '');
    $matricule = htmlspecialchars($_POST['matricule'] ?? '');
    $direction = htmlspecialchars($_POST['direction'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $ville_depart = htmlspecialchars($_POST['ville_depart'] ?? ''); 
    $destination = htmlspecialchars($_POST['destination'] ?? '');
    $date_depart = htmlspecialchars($_POST['date_depart'] ?? '');
    $date_retour = htmlspecialchars($_POST['date_retour'] ?? '');
    $motif = htmlspecialchars($_POST['motif'] ?? '');

    // Validation
    if (empty($destination) || empty($date_depart) || empty($motif)) {
        $message = "<div class='alert alert-warning'><i class='fa-solid fa-triangle-exclamation me-2'></i>Veuillez remplir tous les champs obligatoires!</div>";
    } else {
        try {
            // هنا تم إصلاح الاستعلام ليشمل كل الحقول المتواجدة بالفورم وقاعدة البيانات
            $sql = "INSERT INTO demandes_mission (nom, prenom, matricule, direction, email, ville_depart, destination, date_depart, date_retour, motif, statut, date_creation) 
                    VALUES (:nom, :prenom, :matricule, :direction, :email, :ville_depart, :destination, :date_depart, :date_retour, :motif, 'En attente', NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':matricule' => $matricule,
                ':direction' => $direction,
                ':email' => $email,
                ':ville_depart' => $ville_depart, 
                ':destination' => $destination,
                ':date_depart' => $date_depart,
                ':date_retour' => $date_retour,
                ':motif' => $motif
            ]);

            $demande_id = $pdo->lastInsertId();
            $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <i class='fa-solid fa-circle-check me-2'></i> Demande enregistrée avec succès! ID: <strong>#" . $demande_id . "</strong>
                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                    </div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'><i class='fa-solid fa-circle-xmark me-2'></i>Erreur d'enregistrement: " . $e->getMessage() . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>parc automobile - Nouvelle Demande</title>
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

        .form-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 35px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            font-size: 0.95rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 18px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(0, 86, 179, 0.1);
        }

        .srm-footer {
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 25px 40px;
            font-size: 0.85rem;
            color: #475569;
            width: 100%;
            margin-top: auto;
        }

        .footer-divider {
            height: 3px;
            background: linear-gradient(to right, #0056b3, #e67e22);
            margin-bottom: 20px;
            border-radius: 2px;
        }

        .footer-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .company-fr, .company-ar { flex: 1; }
        .company-fr p, .company-ar p { margin: 3px 0 0 0; font-size: 0.8rem; color: #64748b; }
        .footer-tax { display: flex; justify-content: center; gap: 20px; border-top: 1px solid #f1f5f9; padding-top: 10px; }
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
            <li class="sidebar-item">
                <a href="afficher_demandes.php">
                    <i class="fa-solid fa-clock-history"></i>
                    <span>Historique</span>
                </a>
            </li>
            <li class="sidebar-item active">
                <a href="nouvelle_demande.php">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Nouvelle Demande</span>
                </a>
            </li>
        </ul>
    </aside>

    <div class="main-wrapper">
        <div class="mb-4">
            <h2 class="fw-bold m-0">DEMANDE D'ORDRE DE MISSION</h2>
            <p class="text-muted small m-0">Remplissez les informations requises pour l'agent</p>
        </div>

        <?php echo $message; ?>

        <div class="form-card">
            <form action="nouvelle_demande.php" method="POST">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" class="form-control" placeholder="Nom de l'agent" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom" class="form-control" placeholder="Prénom de l'agent" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Matricule</label>
                        <input type="text" name="matricule" class="form-control" placeholder="Numéro de matricule">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Direction</label>
                        <input type="text" name="direction" class="form-control" placeholder="Direction concernée">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="votre.email@example.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ville de Départ</label>
                        <input type="text" name="ville_depart" class="form-control" placeholder="Ex: Khénifra">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Destination (Lieu de déplacement)</label>
                        <input type="text" name="destination" class="form-control" placeholder="Ex: Siège Béni Mellal" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date de Départ</label>
                        <input type="date" name="date_depart" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date de Retour</label>
                        <input type="date" name="date_retour" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Motif de la Mission</label>
                        <textarea name="motif" class="form-control" rows="3" placeholder="Description du motif de déplacement..." required></textarea>
                    </div>
                    <div class="col-md-12 text-end mt-4">
                        <button type="submit" class="btn btn-primary fw-bold px-5 py-2.5" style="background-color: var(--primary-blue); border-radius: 10px;">
                            <i class="fa-solid fa-paper-plane me-2"></i> Envoyer la demande
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <footer class="srm-footer">
            <div class="footer-divider"></div>
            <div class="footer-top">
                <div class="company-fr">
                    <strong>Société Régionale Multiservices BMK</strong>
                    <p>Bd Omar Ibn Al Khattab, Béni Mellal</p>
                </div>
                <div class="company-ar text-end" dir="rtl">
                    <strong>الشركة الجهوية متعددة الخدمات</strong>
                    <p>شارع عمر بن الخطاب، بني ملال</p>
                </div>
            </div>
            <div class="footer-tax text-center">
                <span>ICE: <strong>003659660000073</strong></span> | <span>IF: <strong>66996362</strong></span>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>