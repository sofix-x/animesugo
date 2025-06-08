<?php
session_start();
include '../../db_connection.php';

header('Content-Type: application/json');

// Security check: only admins can perform this action
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['user_id']) || !isset($data['is_admin'])) {
    echo json_encode(['success' => false, 'error' => 'Неверные данные.']);
    exit;
}

$userIdToUpdate = (int)$data['user_id'];
$newAdminStatus = (int)$data['is_admin'];

// Security check: cannot change the status of the currently logged-in admin
if ($userIdToUpdate === $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'error' => 'Вы не можете изменить свой собственный статус администратора.']);
    exit;
}

// Get the username of the user to be updated
$stmt = $mysqli->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $userIdToUpdate);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Пользователь не найден.']);
    exit;
}
$user = $result->fetch_assoc();

// Security check: cannot change the status of the main 'admin' user
if ($user['username'] === 'admin') {
    echo json_encode(['success' => false, 'error' => 'Статус основного администратора не может быть изменен.']);
    exit;
}

// Update the user's admin status
$stmt = $mysqli->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
$stmt->bind_param("ii", $newAdminStatus, $userIdToUpdate);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка базы данных: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>
