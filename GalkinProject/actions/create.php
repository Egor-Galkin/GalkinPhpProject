<?php
// Подключение функций
require_once '../includes/functions.php';

// Проверка метода и наличия параметра menu
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['menu'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Неверный запрос']);
    exit;
}

$menu = $_POST['menu'];

// Создание сделки
if ($menu === 'deals') {
    $name = trim($_POST['name'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);

    // Проверка обязательного поля "Наименование"
    if ($name === '') {
        echo json_encode(['error' => 'Поле "Наименование" обязательно']);
        exit;
    }

    $id = createDeal($name, $amount);
    if ($id) {
        echo json_encode(['success' => true, 'id' => $id]);
    } else {
        echo json_encode(['error' => 'Ошибка создания сделки']);
    }

// Создание контакта
} elseif ($menu === 'contacts') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');

    // Проверка обязательного поля "Имя"
    if ($firstName === '') {
        echo json_encode(['error' => 'Поле "Имя" обязательно']);
        exit;
    }

    $id = createContact($firstName, $lastName);
    if ($id) {
        echo json_encode(['success' => true, 'id' => $id]);
    } else {
        echo json_encode(['error' => 'Ошибка создания контакта']);
    }

// Некорректное значение menu
} else {
    echo json_encode(['error' => 'Неверное меню']);
}