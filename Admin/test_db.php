<?php
$pdo = new PDO('mysql:host=localhost;dbname=parc_auto', 'root', '');
$q = $pdo->query('DESCRIBE demandes_mission');
print_r($q->fetchAll(PDO::FETCH_ASSOC));
