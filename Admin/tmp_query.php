<?php
require 'includes/db_connection.php';
$stmt = $pdo->query('SELECT id, nom, email FROM demandes_mission ORDER BY id DESC LIMIT 5');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
