<?php
require 'includes/db_connection.php';
$tables = ['vehicle_requests','vehicles','users'];
foreach($tables as $tbl){
    echo "Table: $tbl\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM $tbl");
    foreach($stmt as $row){
        echo $row['Field'].','.$row['Type']."\n";
    }
    echo "\n";
}
?>
