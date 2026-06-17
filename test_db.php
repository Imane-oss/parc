<?php
try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "OK";
} catch (PDOException $e) {
    echo "ERR: " . $e->getMessage();
}
