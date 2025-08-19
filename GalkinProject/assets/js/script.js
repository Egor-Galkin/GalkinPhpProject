document.addEventListener('DOMContentLoaded', () => {
    // Получение элементов интерфейса
    const menuList = document.getElementById('menu-list');
    const listItems = document.getElementById('list-items');
    const createBtn = document.getElementById('create-button');
    const modal = document.getElementById('modal-overlay');
    const modalTitle = document.getElementById('modal-title');
    const createForm = document.getElementById('create-form');
    const formMenu = document.getElementById('form-menu');
    const formId = document.getElementById('form-id');
    const formFields = document.getElementById('form-fields');
    const modalCancel = document.getElementById('modal-cancel');

    // Восстановление позиции скролла списка при загрузке страницы
    if (listItems) {
        const scrollPos = sessionStorage.getItem('listScrollPos');
        if (scrollPos !== null) {
            listItems.scrollTop = parseInt(scrollPos, 10);
        }
        // Сохранение позиции скролла перед уходом со страницы
        window.addEventListener('beforeunload', () => {
            sessionStorage.setItem('listScrollPos', listItems.scrollTop);
        });
    }

    // Обработка клика по элементам меню (Сделки, Контакты)
    menuList.addEventListener('click', (e) => {
        if (e.target.tagName === 'LI') {
            const selectedMenu = e.target.dataset.menu;
            if (!selectedMenu) return;
            window.location.href = `index.php?menu=${selectedMenu}`;
        }
    });

    // Обработка клика по элементам списка (Сделки или Контакты)
    listItems.addEventListener('click', (e) => {
        const li = e.target.closest('li[data-id]');
        if (!li) return;
        // Игнорируем клики по кнопкам внутри li
        if (e.target.closest('button')) return;
        const itemId = li.dataset.id;
        const currentMenu = document.querySelector('#menu-list li.selected').dataset.menu;
        if (!itemId) return;
        window.location.href = `index.php?menu=${currentMenu}&id=${itemId}`;
    });

    // Обработка клика кнопки "Добавить"
    createBtn.addEventListener('click', () => openModal('create'));

    // Делегирование кликов по кнопкам редактирования, удаления и удаления связи
    document.body.addEventListener('click', (event) => {
        if (event.target.classList.contains('edit-button')) {
            const id = event.target.dataset.id;
            const menu = event.target.dataset.menu;
            openModal('edit', menu, id);
        } else if (event.target.classList.contains('delete-button')) {
            const id = event.target.dataset.id;
            const menu = event.target.dataset.menu;
            if (confirm('Вы действительно хотите удалить этот элемент?')) {
                deleteItem(menu, id);
            }
        } else if (event.target.classList.contains('unlink-button')) {
            const dealId = event.target.dataset.dealId;
            const contactId = event.target.dataset.contactId;
            if (dealId && contactId) {
                deleteLinkDealContact(dealId, contactId);
            } else {
                const contactId = event.target.dataset.contactId;
                const dealId = event.target.dataset.dealId;
                if (contactId && dealId) {
                    deleteLinkDealContact(dealId, contactId);
                }
            }
        }
    });

    // Обработка закрытия модального окна по кнопке "Отмена"
    modalCancel.addEventListener('click', () => {
        modal.style.display = 'none';
        createForm.reset();
        formId.value = '';
    });

    // Закрытие модального окна при клике вне его содержимого
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            createForm.reset();
            formId.value = '';
        }
    });

    // Обработка отправки формы создания/редактирования
    createForm.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(createForm);
        let url = 'actions/create.php';
        if(formId.value) url = 'actions/update.php';
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(resp => resp.json())
        .then(data => {
            if(data.success) {
                alert('Операция прошла успешно.');
                modal.style.display = 'none';
                createForm.reset();
                formId.value = '';
                window.location.href = `index.php?menu=${formMenu.value}`;
            } else if(data.error) {
                alert('Ошибка: ' + data.error);
            } else {
                alert('Неизвестная ошибка');
            }
        })
        .catch(() => alert('Ошибка при отправке запроса'));
    });

    // Открытие модального окна для создания или редактирования
    function openModal(mode, menu, id) {
        menu = menu || document.querySelector('#menu-list li.selected').dataset.menu || 'deals';
        formMenu.value = menu;
        formId.value = '';
        modalTitle.textContent = (mode === 'edit') ? 'Редактировать элемент' : 'Создать новый элемент';
        if (mode === 'edit' && id) {
            fetch(`actions/get_item.php?menu=${menu}&id=${id}`)
                .then(resp => resp.json())
                .then(data => {
                    if (data.success) {
                        fillForm(data.item, menu);
                        modal.style.display = 'flex';
                    } else {
                        alert('Ошибка загрузки объекта');
                    }
                })
                .catch(() => alert('Ошибка запроса'));
        } else {
            fillForm(null, menu);
            modal.style.display = 'flex';
        }
    }

    // Заполнение формы данными для редактирования или пустой формой для создания
    function fillForm(item, menu) {
        let html = '';
        if(menu === 'deals') {
            html += `<label>Наименование: <input type="text" name="name" required value="${item ? item.name : ''}"></label><br>`;
            html += `<label>Сумма: <input type="number" step="0.01" name="amount" required value="${item ? item.amount : ''}"></label><br>`;
        } else {
            html += `<label>Имя: <input type="text" name="first_name" required value="${item ? item.first_name : ''}"></label><br>`;
            html += `<label>Фамилия: <input type="text" name="last_name" required value="${item ? item.last_name : ''}"></label><br>`;
        }
        formFields.innerHTML = html;
        formId.value = item ? item.id : '';
    }

    // Удаление сделки или контакта
    function deleteItem(menu, id) {
        const data = new FormData();
        data.append('menu', menu);
        data.append('id', id);
        fetch('actions/delete.php', {
            method: 'POST',
            body: data
        })
        .then(resp => resp.json())
        .then(data => {
            if(data.success) {
                alert('Элемент удалён');
                window.location.href = `index.php?menu=${menu}`;
            } else {
                alert('Ошибка: ' + (data.error || 'неизвестная ошибка'));
            }
        })
        .catch(() => alert('Ошибка запроса'));
    }

    // Удаление связи между сделкой и контактом
    function deleteLinkDealContact(dealId, contactId) {
        const data = new FormData();
        data.append('deal_id', dealId);
        data.append('contact_id', contactId);
        fetch('actions/delete_link.php', {
            method: 'POST',
            body: data
        })
        .then(resp => resp.json())
        .then(data => {
            if(data.success) {
                alert('Связь удалена');
                const currentMenu = document.querySelector('#menu-list li.selected').dataset.menu;
                const currentId = document.querySelector('#list-items li.selected').dataset.id;
                window.location.href = `index.php?menu=${currentMenu}&id=${currentId}`;
            } else {
                alert('Ошибка: ' + (data.error || 'неизвестная ошибка'));
            }
        })
        .catch(() => alert('Ошибка запроса'));
    }

    // Обработка кнопки добавления связи "сделка -> контакт"
    document.body.addEventListener('click', (e) => {
        if (e.target.id === 'deal-add-link-btn') {
            const dealId = e.target.dataset.dealId;
            const select = document.getElementById('deal-contact-select');
            if (!select.value) {
                alert('Выберите контакт');
                return;
            }
            const contactId = select.value;
            const data = new FormData();
            data.append('deal_id', dealId);
            data.append('contact_id', contactId);
            fetch('actions/add_link.php', {
                method: 'POST',
                body: data
            })
            .then(resp => resp.json())
            .then(data => {
                if(data.success) {
                    alert('Связь добавлена');
                    window.location.href = `index.php?menu=deals&id=${dealId}`;
                } else {
                    alert('Ошибка: ' + (data.error || 'неизвестная ошибка'));
                }
            })
            .catch(() => alert('Ошибка запроса'));
        } else if (e.target.id === 'contact-add-link-btn') {
            // Обработка кнопки добавления связи "контакт -> сделка"
            const contactId = e.target.dataset.contactId;
            const select = document.getElementById('contact-deal-select');
            if (!select.value) {
                alert('Выберите сделку');
                return;
            }
            const dealId = select.value;
            const data = new FormData();
            data.append('deal_id', dealId);
            data.append('contact_id', contactId);
            fetch('actions/add_link.php', {
                method: 'POST',
                body: data
            })
            .then(resp => resp.json())
            .then(data => {
                if(data.success) {
                    alert('Связь добавлена');
                    window.location.href = `index.php?menu=contacts&id=${contactId}`;
                } else {
                    alert('Ошибка: ' + (data.error || 'неизвестная ошибка'));
                }
            })
            .catch(() => alert('Ошибка запроса'));
        }
    });
});