<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="assets/css/log-reg.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="index.php">Главная</a></li>
            <li><a href="login.php">Войти</a></li>
            <li class="cart-item">
                <a href="#" onclick="toggleCart()"><span class="cart-icon">🛒</span></a>
            </li>
        </ul>
    </nav>
</header>

<main>
    <section class="auth-card">
        <h2>Регистрация</h2>
        <?php
        session_start(); // Необходимо для доступа к $_SESSION
        if (isset($_SESSION['error_message'])) {
            echo '<p class="message error">' . $_SESSION['error_message'] . '</p>';
            unset($_SESSION['error_message']); // Очищаем сообщение после отображения
        }
        if (isset($_SESSION['success_message'])) {
            echo '<p class="message success">' . $_SESSION['success_message'] . '</p>';
            unset($_SESSION['success_message']); // Очищаем сообщение после отображения
        }
        ?>
        <form action="assets/vendor/signup.php" method="post">
        <div class="form-group">
        <label for="username">Логин:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div class="form-group">
        <label for="confirm_password">Повторите пароль:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
    </div>
    <button type="submit" class="submit-button">Зарегистрироваться</button>
            <a href="login.php" class="footer-link">Уже есть аккаунт? Войти</a>
        </form>
    </section>
</main>

<footer>
    <p>&copy; 2024 Интернет-каталог товаров</p>
</footer>

</body>
</html>
