<?php
session_start();
require_once(__DIR__ . '/db_connexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $fullname = trim($prenom . ' ' . $nom);

    if (!empty($fullname) && !empty($email) && !empty($password)) {
        try {
            
            $checkQuery = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $checkQuery->execute(['email' => $email]);
            $existingUser = $checkQuery->fetch();

            if ($existingUser) {
                
                echo "<script>alert('Cet email est déjà utilisé !'); window.location.href='inscription.html';</script>";
                exit();
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $insertQuery = $pdo->prepare("INSERT INTO users (fullname, email, password, role) VALUES (:fullname, :email, :passwordHash, 'user')");
            $insertQuery->execute([
                'fullname' => $fullname,
                'email' => $email,
                'passwordHash' => $passwordHash
            ]);

            
            echo "<script>alert('Inscription réussie ! Vous pouvez maintenant vous connecter.'); window.location.href='CONNEXION.html';</script>";
            exit();

        } catch (PDOException $e) {
            die("Erreur lors de l'inscription : " . $e->getMessage());
        }
    } else {
        echo "<script>alert('Veuillez remplir tous les champs !'); window.location.href='inscription.html';</script>";
        exit();
    }
}
?>
