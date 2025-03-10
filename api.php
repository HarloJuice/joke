<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "advice_app";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getCategories':
        $result = $conn->query("SELECT * FROM categories");
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        echo json_encode($categories);
        break;

    case 'getAll':
        $categoryId = $_GET['category_id'] ?? '';
        if ($categoryId) {
            $stmt = $conn->prepare("SELECT advices.id, categories.name AS category, advices.advice FROM advices JOIN categories ON advices.category_id = categories.id WHERE advices.category_id = ?");
            $stmt->bind_param("i", $categoryId);
        } else {
            $stmt = $conn->prepare("SELECT advices.id, categories.name AS category, advices.advice FROM advices JOIN categories ON advices.category_id = categories.id");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $advices = [];
        while ($row = $result->fetch_assoc()) {
            $advices[] = $row;
        }
        echo json_encode($advices);
        break;

    case 'getOne':
        $id = $_GET['id'];
        $stmt = $conn->prepare("SELECT advices.id, advices.category_id, advices.advice FROM advices WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            echo json_encode(['error' => 'Порада не знайдена']);
        }
        break;

    case 'add':
        $data = json_decode(file_get_contents('php://input'), true);
        $categoryId = $data['category_id'];
        $advice = $data['advice'];
        $stmt = $conn->prepare("INSERT INTO advices (category_id, advice) VALUES (?, ?)");
        $stmt->bind_param("is", $categoryId, $advice);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'edit':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'];
        $categoryId = $data['category_id'];
        $advice = $data['advice'];
        $stmt = $conn->prepare("UPDATE advices SET category_id = ?, advice = ? WHERE id = ?");
        $stmt->bind_param("isi", $categoryId, $advice, $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'delete':
        $id = $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM advices WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    default:
        echo json_encode(['error' => 'Невідома дія']);
        break;
}

$conn->close();
?>