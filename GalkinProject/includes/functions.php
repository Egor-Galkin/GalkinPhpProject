<?php
// Подключение файла с настройками и объектом PDO для БД
require_once 'db.php';

// Получение всех сделок, отсортированных по названию
function getAllDeals() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM deals ORDER BY name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получение всех контактов, отсортированных по имени и фамилии
function getAllContacts() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY first_name ASC, last_name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получение одной сделки по ID вместе с привязанными контактами
function getDealById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM deals WHERE id = ?");
    $stmt->execute([$id]);
    $deal = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($deal) {
        $stmt = $pdo->prepare("SELECT contacts.* FROM contacts 
                               INNER JOIN deal_contact ON contacts.id = deal_contact.contact_id
                               WHERE deal_contact.deal_id = ?");
        $stmt->execute([$id]);
        $deal['contacts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $deal;
}

// Получение одного контакта по ID вместе с привязанными сделками
function getContactById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->execute([$id]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($contact) {
        $stmt = $pdo->prepare("SELECT deals.* FROM deals
                               INNER JOIN deal_contact ON deals.id = deal_contact.deal_id
                               WHERE deal_contact.contact_id = ?");
        $stmt->execute([$id]);
        $contact['deals'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return $contact;
}

// Создание новой сделки с указанием названия и суммы, возвращает ID созданной записи
function createDeal($name, $amount) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO deals (name, amount) VALUES (?, ?)");
    $stmt->execute([$name, $amount]);
    return $pdo->lastInsertId();
}

// Создание нового контакта с именем и фамилией, возвращает ID созданной записи
function createContact($firstName, $lastName) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO contacts (first_name, last_name) VALUES (?, ?)");
    $stmt->execute([$firstName, $lastName]);
    return $pdo->lastInsertId();
}

// Обновление сделки по ID, изменение названия и суммы
function updateDeal($id, $name, $amount) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE deals SET name = ?, amount = ? WHERE id = ?");
    $stmt->execute([$name, $amount, $id]);
}

// Обновление контакта по ID, изменение имени и фамилии
function updateContact($id, $firstName, $lastName) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE contacts SET first_name = ?, last_name = ? WHERE id = ?");
    $stmt->execute([$firstName, $lastName, $id]);
}

// Удаление сделки и всех её связей с контактами по ID
function deleteDeal($id) {
    global $pdo;
    $pdo->prepare("DELETE FROM deal_contact WHERE deal_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM deals WHERE id = ?")->execute([$id]);
}

// Удаление контакта и всех его связей со сделками по ID
function deleteContact($id) {
    global $pdo;
    $pdo->prepare("DELETE FROM deal_contact WHERE contact_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM contacts WHERE id = ?")->execute([$id]);
}

// Добавление связи между сделкой и контактом, если она еще не существует
function addLinkDealContact($dealId, $contactId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM deal_contact WHERE deal_id = ? AND contact_id = ?");
    $stmt->execute([$dealId, $contactId]);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO deal_contact (deal_id, contact_id) VALUES (?, ?)");
        $stmt->execute([$dealId, $contactId]);
    }
}

// Удаление связи между сделкой и контактом
function deleteLinkDealContact($dealId, $contactId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM deal_contact WHERE deal_id = ? AND contact_id = ?");
    $stmt->execute([$dealId, $contactId]);
}