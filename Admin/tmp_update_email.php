<?php
require 'includes/db_connection.php';
// For testing: set all emails to the Resend account owner email
$stmt = $pdo->exec("UPDATE demandes_mission SET email = 'zianiiman060@gmail.com'");
echo "Updated " . $stmt . " rows. Now test ACCEPTER - the email will go to zianiiman060@gmail.com";
