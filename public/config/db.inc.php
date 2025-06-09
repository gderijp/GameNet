<?php

$host = '127.0.0.1';
$db   = 'gamenet';
$user = 'bit_academy';
$pass = 'bit_academy';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $err) {
    echo "Database connection error: " . $err->getMessage();
    exit();
}
