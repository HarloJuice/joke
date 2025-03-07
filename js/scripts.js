// Функція для редагування поради
function editAdvice(id) {
    fetch(`api.php?action=getOne&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.id && data.topic && data.advice) {
                document.getElementById("editId").value = data.id;
                document.getElementById("editTopic").value = data.topic;
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
// Збереження змін
document.getElementById("editAdviceForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const id = document.getElementById("editId").value;
    const topic = document.getElementById("editTopic").value;
    const advice = document.getElementById("editAdvice").value;

    fetch('api.php?action=edit', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem("token")}`
        },
        body: JSON.stringify({ id, topic, advice })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadAdvices();
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
        }
    });
});

// Модальне вікно для редагування
document.addEventListener("DOMContentLoaded", function () {
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.addEventListener('hidden.bs.modal', function () {
            document.getElementById("editAdviceForm").reset();
        });
    }
});