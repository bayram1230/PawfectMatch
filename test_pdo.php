<?php

$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=pawfectmatch;charset=utf8mb4';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass);
    echo "PDO CONNECTION OK\n";
} catch (Throwable $e) {
    echo "PDO CONNECTION FAILED:\n";
    echo $e->getMessage();
}
