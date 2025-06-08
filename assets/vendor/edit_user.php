<?php
session_start();
header('Content-Type: application/json');
include '../../db_connection.php';

/*
 *  Edit user endpoint
 *  Accepts JSON: { "user_id": 3, "username": "newName", "password": "newPass" }
 *  Rules:
 *   - Only admins can edit any user.
 *   - A non-admin can only edit their own account.
 *   - Username must be unique.
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Метод не поддерживается.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['user_id']) || !isset($data['username'])) {
    echo json_encode(['success' => false, 'error' => 'Неверные входные данные.']);
    exit;
}

$userId   = (int)$data['user_id'];
$username = trim($data['username']);
$password = isset($data['password']) ? trim($data['password']) : '';

if ($username === '') {
    echo json_encode(['success' => false, 'error' => 'Имя пользователя не может быть пустым.']);
    exit;
}

/* -------- PERMISSION CHECKS -------- */
$currentUserId = $_SESSION['user_id'] ?? 0;
$isAdmin       = !empty($_SESSION['is_admin']);

if (!$isAdmin && $currentUserId !== $userId) {
    echo json_encode(['success' => false, 'error' => 'У вас нет прав для изменения этого пользователя.']);
    exit;
}

/* -------- VALIDATE UNIQUE USERNAME -------- */
$stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? AND id <> ?");
$stmt->bind_param('si', $username, $userId);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Пользователь с таким именем уже существует.']);
    $stmt->close();
    exit;
}
$stmt->close();

/* -------- BUILD UPDATE QUERY -------- */
$fields = ['username = ?'];
$params = ['s', &$username];

if ($password !== '') {
    $hashed  = password_hash($password, PASSWORD_DEFAULT);
    $fields[] = 'password = ?';
    $params[0] .= 's';         // add type
    $params[] = &$hashed;      // add variable by reference
}

$setClause = implode(', ', $fields);
$sql       = "UPDATE users SET $setClause WHERE id = ?";
$params[0] .= 'i';
$params[]   = &$userId;

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Ошибка подготовки запроса.']);
    exit;
}
call_user_func_array([$stmt, 'bind_param'], $params);

if ($stmt->execute()) {
    // Если пользователь редактирует свой аккаунт, обновим сессию
    if ($userId === $currentUserId) {
        $_SESSION['username'] = $username;
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка БД: ' . $stmt->error]);
}
$stmt->close();
$mysqli->close();
?>
