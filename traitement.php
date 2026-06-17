<?php
session_start();
require_once(__DIR__ . '/db_connexion.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        try {
            
            $query = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $query->execute(['email' => $email]);
            $user = $query->fetch();

            if ($user) {
                
                if (password_verify($password, $user['password']) || $password === $user['password']) {
                    
                
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role']; 

                    
    
                    if ($user['role'] == 'admin') {
                        header("Location: Admin/dashboard.php"); 
                        exit();
                    } else {
                        header("Location: demande/templates/afficher_demandes.php");
                        exit();
                    }

                } else {
                    
                    header("Location: CONNEXION.html?error=password");
                    exit();
                }
            } else {
            
                header("Location: CONNEXION.html?error=user_not_found");
                exit();
            }
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    } else {
        header("Location: CONNEXION.html?error=empty");
        exit();
    }
}
?>