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

// Обновление сделки
if ($menu === 'deals') {
    $name = trim($_POST['name'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);

    // Проверка обязательного поля "Наименование"
    if ($name === '') {
        echo json_encode(['error' => 'Поле "Наименование" обязательно']);
        exit;
    }

    updateDeal($id, $name, $amount);
    echo json_encode(['success' => true]);

// Обновление контакта
} elseif ($menu === 'contacts') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');

    // Проверка обязательного поля "Имя"
    if ($firstName === '') {
        echo json_encode(['error' => 'Поле "Имя" обязательно']);
        exit;
    }

    updateContact($id, $firstName, $lastName);
    echo json_encode(['success' => true]);

// Некорректное значение menu
} else {
    echo json_encode(['error' => 'Неверное меню']);
}