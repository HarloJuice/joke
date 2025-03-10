// Функція для завантаження категорій
function loadCategories() {
    fetch('api.php?action=getCategories')
        .then(response => response.json())
        .then(data => {
            const categorySelect = document.getElementById("category");
            const categoryFilter = document.getElementById("categoryFilter");
            const editCategorySelect = document.getElementById("editCategory");

            // Очищаємо списки
            categorySelect.innerHTML = '<option value="">Оберіть категорію</option>';
            categoryFilter.innerHTML = '<option value="">Всі категорії</option>';
            editCategorySelect.innerHTML = '<option value="">Оберіть категорію</option>';

            // Заповнюємо списки категоріями
            data.forEach(category => {
                const option = document.createElement("option");
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option.cloneNode(true));
                categoryFilter.appendChild(option.cloneNode(true));
                editCategorySelect.appendChild(option.cloneNode(true));
            });
        });
}

// Функція для завантаження порад з фільтрацією
function loadAdvices() {
    const categoryId = document.getElementById("categoryFilter").value;
    const url = categoryId ? `api.php?action=getAll&category_id=${categoryId}` : 'api.php?action=getAll';

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const adviceList = document.getElementById("adviceList");
            adviceList.innerHTML = "";
            data.forEach(advice => {
                const adviceItem = document.createElement("div");
                adviceItem.className = "card mb-3";
                adviceItem.innerHTML = `
                    <div class="card-body">
                        <h5 class="card-title">${advice.category}</h5>
                        <p class="card-text">${advice.advice}</p>
                        <button class="btn btn-warning btn-sm" onclick="editAdvice(${advice.id})">Редагувати</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteAdvice(${advice.id})">Видалити</button>
                    </div>
                `;
                adviceList.appendChild(adviceItem);
            });
        });
}

// Функція для редагування поради
function editAdvice(id) {
    fetch(`api.php?action=getOne&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.id && data.category_id && data.advice) {
                document.getElementById("editId").value = data.id;
                document.getElementById("editCategory").value = data.category_id;
                document.getElementById("editAdvice").value = data.advice;

                // Відкриваємо модальне вікно
                const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
            } else {
                console.error("Дані не отримано або вони неповні:", data);
            }
        })
        .catch(error => {
            console.error("Помилка при отриманні даних:", error);
        });
}

// Функція для додавання поради
document.getElementById("addAdviceForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const categoryId = document.getElementById("category").value;
    const advice = document.getElementById("advice").value;

    fetch('api.php?action=add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem("token")}`
        },
        body: JSON.stringify({ category_id: categoryId, advice })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadAdvices();
            document.getElementById("addAdviceForm").reset();
        }
    });
});

// Функція для збереження змін при редагуванні
document.getElementById("editAdviceForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const id = document.getElementById("editId").value;
    const categoryId = document.getElementById("editCategory").value;
    const advice = document.getElementById("editAdvice").value;

    fetch('api.php?action=edit', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem("token")}`
        },
        body: JSON.stringify({ id, category_id: categoryId, advice })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadAdvices();
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
        }
    });
});

// Функція для видалення поради
function deleteAdvice(id) {
    fetch(`api.php?action=delete&id=${id}`, {
        method: 'DELETE',
        headers: {
            'Authorization': `Bearer ${localStorage.getItem("token")}`
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadAdvices();
        }
    });
}

// Завантажуємо категорії та поради при завантаженні сторінки
loadCategories();
loadAdvices();