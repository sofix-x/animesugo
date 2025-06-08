<?php
session_start();
header('Content-Type: application/json');
include '../../db_connection.php';

/*
 *  Delete user endpoint
 *  Accepts JSON: { "user_id": 5 }
 *  Rules:
 *   - Only admins can delete users.
 *   - Cannot delete yourself.
 *   - Cannot delete the main 'admin' account.
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Метод не поддерживается.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Неверные входные данные.']);
    exit;
}

$userIdToDelete = (int)$data['user_id'];
$currentUserId  = $_SESSION['user_id'] ?? 0;
$isAdmin        = !empty($_SESSION['is_admin']);

if (!$isAdmin) {
    echo json_encode(['success' => false, 'error' => 'У вас нет прав для удаления пользователей.']);
    exit;
}
if ($userIdToDelete === $currentUserId) {
    echo json_encode(['success' => false, 'error' => 'Нельзя удалить самого себя.']);
    exit;
}

/* Проверяем, что не удаляем основного админа */
$stmt = $mysqli->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param('i', $userIdToDelete);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Пользователь не найден.']);
    exit;
}
$row = $result->fetch_assoc();
if ($row['username'] === 'admin') {
    echo json_encode(['success' => false, 'error' => 'Нельзя удалить основного администратора.']);
    exit;
}
$stmt->close();

/* Удаляем пользователя */
$stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param('i', $userIdToDelete);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка БД: ' . $stmt->error]);
}
$stmt->close();
$mysqli->close();
?>
