<?php
require 'includes/db_connection.php';
try {
    $pdo->exec("ALTER TABLE demandes_mission ADD COLUMN email VARCHAR(255) NULL AFTER prenom");
    echo "Column 'email' added successfully.";
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Column 'email' already exists.";
    } else {
        echo "Error adding column: " . $e->getMessage();
    }
}
