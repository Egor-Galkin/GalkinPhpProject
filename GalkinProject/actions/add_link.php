<?php
// Подключение функций
require_once '../includes/functions.php';

// Проверка метода и обязательных параметров
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['deal_id']) || !isset($_POST['contact_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Неверный запрос']);
    exit;
}

$dealId = intval($_POST['deal_id']);
$contactId = intval($_POST['contact_id']);

// Проверка корректности ID
if ($dealId <= 0 || $contactId <= 0) {
    echo json_encode(['error' => 'Некорректные ID']);
    exit;
}

// Добавление связи между сделкой и контактом
addLinkDealContact($dealId, $contactId);

// Успешный ответ
echo json_encode(['success' => true]);