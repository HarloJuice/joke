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
    case 'login':
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'];
        $password = $data['password'];

        $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $token = bin2hex(random_bytes(16));
                $stmt = $conn->prepare("UPDATE admins SET token = ? WHERE id = ?");
                $stmt->bind_param("si", $token, $row['id']);
                $stmt->execute();
                echo json_encode(['success' => true, 'token' => $token]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
        break;

    case 'add':
        $data = json_decode(file_get_contents('php://input'), true);
        $topic = $data['topic'];
        $advice = $data['advice'];
        $stmt = $conn->prepare("INSERT INTO advices (topic, advice) VALUES (?, ?)");
        $stmt->bind_param("ss", $topic, $advice);
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

    case 'getAll':
        $result = $conn->query("SELECT * FROM advices");
        $advices = [];
        while ($row = $result->fetch_assoc()) {
            $advices[] = $row;
        }
        echo json_encode($advices);
        break;
        case 'getOne':
            $id = $_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM advices WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo json_encode($row); // Повертаємо дані у форматі JSON
            } else {
                echo json_encode(['error' => 'Порада не знайдена']);
            }
            break;
        
        default:
            $topic = $_GET['topic'] ?? '';
            if ($topic) {
                $stmt = $conn->prepare("SELECT advice FROM advices WHERE topic = ?");
                $stmt->bind_param("s", $topic);
                $stmt->execute();
                $result = $stmt->get_result();
                $advices = [];
                while ($row = $result->fetch_assoc()) {
                    $advices[] = $row['advice'];
                }
                if (!empty($advices)) {
                    $randomAdvice = $advices[array_rand($advices)];
                    echo json_encode(['advice' => $randomAdvice]);
                } else {
                    echo json_encode(['error' => 'Порад не знайдено']);
                }
            } else {
                echo json_encode(['error' => 'Тема не вказана']);
            }
            break;

}

$conn->close();
?>