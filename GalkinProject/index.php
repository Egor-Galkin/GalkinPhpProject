<?php
// Подключение основных функций и работы с БД
require_once 'includes/functions.php';

// Получение выбранного меню (deals или contacts), по умолчанию 'deals'
$menu = $_GET['menu'] ?? 'deals';

// Получение списка элементов для выбранного меню
if ($menu === 'deals') {
    $listItems = getAllDeals(); // Получить все сделки
} else {
    $listItems = getAllContacts(); // Получить все контакты
}

// Определение выбранного элемента: из GET или первый в списке по умолчанию
$selectedId = isset($_GET['id']) ? intval($_GET['id']) : (count($listItems) ? $listItems[0]['id'] : 0);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>GalkinProject - Управление Сделками и Контактами</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
<div class="container">
    <h1>Управление Сделками и Контактами</h1>
    <div class="knowledge-base">
        <!-- Блок меню выбора "Сделки" или "Контакты" -->
        <div class="panel menu-panel">
            <h2>Меню</h2>
            <ul id="menu-list">
                <li data-menu="deals" class="<?= $menu === 'deals' ? 'selected' : '' ?>">Сделки</li>
                <li data-menu="contacts" class="<?= $menu === 'contacts' ? 'selected' : '' ?>">Контакты</li>
            </ul>
        </div>

        <!-- Блок списка элементов выбранного меню -->
        <div class="panel list-panel">
            <h2>Список</h2>
            <ul id="list-items">
                <?php foreach ($listItems as $item): ?>
                    <li data-id="<?= htmlspecialchars($item['id']) ?>" class="<?= ($item['id'] == $selectedId) ? 'selected' : '' ?>">
                        <span class="item-text">
                            <?php
                            // Вывод элемента: для сделок наименование, для контактов имя и фамилию
                            if ($menu === 'deals') {
                                echo htmlspecialchars($item['name']);
                            } else {
                                echo htmlspecialchars($item['first_name'] . ' ' . $item['last_name']);
                            }
                            ?>
                        </span>
                        <span class="item-actions">
                            <!-- Кнопки редактирования и удаления элемента -->
                            <button class="edit-button" title="Редактировать" data-menu="<?= $menu ?>" data-id="<?= $item['id'] ?>">✎</button>
                            <button class="delete-button" title="Удалить" data-menu="<?= $menu ?>" data-id="<?= $item['id'] ?>">✖</button>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <!-- Кнопка создания нового элемента -->
            <button id="create-button">Добавить</button>
        </div>

        <!-- Блок содержимого выбранного элемента -->
        <div class="panel content-panel">
            <h2>Содержимое</h2>
            <div id="content-area">
                <?php
                if ($menu === 'deals') {
                    // Получение данных выбранной сделки с контактами
                    $deal = getDealById($selectedId);
                    if ($deal) {
                        // Заголовок с именем сделки и суммой в скобках
                        echo '<h3>' . htmlspecialchars($deal['name']) . ' (' . number_format($deal['amount'], 2, '.', '') . ')</h3>';
                        echo '<h4>Связанные контакты</h4>';
                        // Список связанных контактов
                        echo '<ul class="content-list">';
                        if (!empty($deal['contacts'])) {
                            foreach ($deal['contacts'] as $c) {
                                echo '<li>';
                                echo '<span class="content-info">' . htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) . '</span>';
                                // Кнопка удаления связи
                                echo '<button class="unlink-button" title="Удалить связь" data-deal-id="' . $deal['id'] . '" data-contact-id="' . $c['id'] . '">✖</button>';
                                echo '</li>';
                            }
                        } else {
                            echo '<li>Связанных контактов нет</li>';
                        }
                        echo '</ul>';

                        // Форма добавления новой связи с контактом (с подписью сверху)
                        echo '<div class="assoc-add-container">';
                        echo '<label for="deal-contact-select">Добавить контакт к сделке:</label>';
                        echo '<div class="assoc-add-controls">';
                        echo '<select id="deal-contact-select" name="contact_id" required>';
                        $allContacts = getAllContacts();
                        $connectedIds = array_column($deal['contacts'], 'id');
                        foreach ($allContacts as $contact) {
                            if (!in_array($contact['id'], $connectedIds)) {
                                echo '<option value="' . htmlspecialchars($contact['id']) . '">' . htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']) . '</option>';
                            }
                        }
                        echo '</select>';
                        echo '<button id="deal-add-link-btn" data-deal-id="' . $deal['id'] . '">Добавить</button>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    // Получение данных выбранного контакта со сделками
                    $contact = getContactById($selectedId);
                    if ($contact) {
                        // Заголовок с именем и фамилией контакта
                        echo '<h3>' . htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']) . '</h3>';
                        echo '<h4>Связанные сделки</h4>';
                        // Список связанных сделок
                        echo '<ul class="content-list">';
                        if (!empty($contact['deals'])) {
                            foreach ($contact['deals'] as $d) {
                                echo '<li>';
                                echo '<span class="content-info">' . htmlspecialchars($d['name']) . '</span>';
                                // Кнопка удаления связи
                                echo '<button class="unlink-button" title="Удалить связь" data-contact-id="' . $contact['id'] . '" data-deal-id="' . $d['id'] . '">✖</button>';
                                echo '</li>';
                            }
                        } else {
                            echo '<li>Связанных сделок нет</li>';
                        }
                        echo '</ul>';

                        // Форма добавления новой связи со сделкой
                        echo '<div class="assoc-add-container">';
                        echo '<label for="contact-deal-select">Добавить сделку к контакту:</label>';
                        echo '<div class="assoc-add-controls">';
                        echo '<select id="contact-deal-select" name="deal_id" required>';
                        $allDeals = getAllDeals();
                        $connectedIds = array_column($contact['deals'], 'id');
                        foreach ($allDeals as $deal) {
                            if (!in_array($deal['id'], $connectedIds)) {
                                echo '<option value="' . htmlspecialchars($deal['id']) . '">' . htmlspecialchars($deal['name']) . '</option>';
                            }
                        }
                        echo '</select>';
                        echo '<button id="contact-add-link-btn" data-contact-id="' . $contact['id'] . '">Добавить</button>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для создания и редактирования -->
<div id="modal-overlay" style="display:none;">
  <div id="modal-window">
    <h3 id="modal-title"></h3>
    <form id="create-form">
      <input type="hidden" name="menu" id="form-menu" value="deals" />
      <input type="hidden" name="id" id="form-id" value="" />
      <div id="form-fields"></div>
      <button type="submit">Сохранить</button>
      <button type="button" id="modal-cancel">Отмена</button>
    </form>
  </div>
</div>
<script src="assets/js/script.js"></script>
</body>
</html>