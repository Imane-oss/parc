<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=parc_auto;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $exists = $stmt->fetch();
    echo $exists ? 'TABLE_EXISTS' : 'TABLE_MISSING';
} catch (PDOException $e) {
    echo 'ERR: ' . $e->getMessage();
}
