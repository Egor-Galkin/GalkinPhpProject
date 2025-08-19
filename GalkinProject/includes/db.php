<?php
// Подключение к базе данных
$dbHost = 'localhost';
$dbName = 'galkinproject';
$dbUser = 'root'; // Замените при необходимости
$dbPass = '';     // Замените при необходимости

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}