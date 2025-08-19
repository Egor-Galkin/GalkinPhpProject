<?php
// Подключение функций
require_once '../includes/functions.php';

// Проверка метода и обязательных GET-параметров
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['menu']) || !isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Неверный запрос']);
    exit;
}

$menu = $_GET['menu'];
$id = intval($_GET['id']);

// Проверка корректности ID
if ($id <= 0) {
    echo json_encode(['error' => 'Некорректный ID']);
    exit;
}

// Получение данных сделки или контакта
if ($menu === 'deals') {
    $item = getDealById($id);
} elseif ($menu === 'contacts') {
    $item = getContactById($id);
} else {
    echo json_encode(['error' => 'Неверное меню']);
    exit;
}

// Проверка успешности запроса
if (!$item) {
    echo json_encode(['error' => 'Элемент не найден']);
    exit;
}

// Возврат данных в JSON
echo json_encode(['success' => true, 'item' => $item]);