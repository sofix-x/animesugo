<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="assets/css/log-reg.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="index.php">Главная</a></li>
            <li><a href="register.php">Зарегистрироваться</a></li>
            <li class="cart-item">
                <a href="#" onclick="toggleCart()"><span class="cart-icon">🛒</span></a>
            </li>
        </ul>
    </nav>
</header>

<main>
<section class="auth-card">
    <h2>Вход</h2>
    <?php
    // session_start() уже должен быть вызван в signin.php или register.php перед редиректом сюда
    // но для прямого доступа к login.php, если сессия еще не начата, лучше добавить
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['error_message'])) {
        echo '<p class="message error">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
        unset($_SESSION['error_message']); // Очищаем сообщение после отображения
    }
    if (isset($_SESSION['success_message'])) { // На случай, если будет сообщение об успехе, например, после выхода
        echo '<p class="message success">' . htmlspecialchars($_SESSION['success_message']) . '</p>';
        unset($_SESSION['success_message']); // Очищаем сообщение после отображения
    }
    ?>
    <form action="assets/vendor/signin.php" method="post">
    <div class="form-group">
        <label for="username">Логин:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="submit-button">Войти</button>
    </form>
</section>

</main>

<footer>
    <p>&copy; 2024 Интернет-каталог товаров</p>
</footer>

</body>
</html>
