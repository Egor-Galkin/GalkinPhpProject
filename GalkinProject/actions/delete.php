<?php
// Подключение функций
require_once '../includes/functions.php';

// Проверка метода и обязательных параметров
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['menu']) || !isset($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Неверный запрос']);
    exit;
}

$menu = $_POST['menu'];
$id = intval($_POST['id']);

// Проверка корректности ID
if ($id <= 0) {
    echo json_encode(['error' => 'Некорректный ID']);
    exit;
}

// Удаление сделки
if ($menu === 'deals') {
    deleteDeal($id);
    echo json_encode(['success' => true]);

// Удаление контакта
} elseif ($menu === 'contacts') {
    deleteContact($id);
    echo json_encode(['success' => true]);

// Некорректное значение menu
} else {
    echo json_encode(['error' => 'Неверное меню']);
}