<?php
require 'includes/db_connection.php';
$stmt = $pdo->query('DESCRIBE demandes_mission');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
