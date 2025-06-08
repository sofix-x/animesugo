<?php
session_start();
// Проверка на наличие сообщений и вывод их

include 'db_connection.php';

// Запрос на получение первых 4 товаров
$sql = "SELECT name, description, price, image FROM products LIMIT 4";
$result = $mysqli->query($sql);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Интернет-каталог товаров</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <nav class="container">
        <ul>
            <li><a href="index.php">Главная</a></li>
            <li><a href="tovars.php">Товары</a></li>
            <li><a href="abous_us.php">О нас</a></li>
            <li class="cart-item">
                <a href="#" onclick="toggleCart()"><span class="cart-icon">🛒</span></a>
            </li>
            <?php if (isset($_SESSION['username'])): ?>
                <?php if ($_SESSION['is_admin']): ?>
                    <li><a href="admin.php">Админ панель</a></li>
                
                <?php endif; ?>
                <li><a href="assets/vendor/logout.php">Выход</a></li>
            <?php else: ?>
                <li><a href="register.php">Зарегистрироваться</a></li>
                <li><a href="login.php">Войти</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<!-- Всплывающее окно корзины -->
<div class="cart-popup" id="cartPopup" style="display: none;">
    <h2>Корзина</h2>
    <div class="cart-items" id="cartItems">
        <!-- Список товаров в корзине будет здесь -->
    </div>
    <button onclick="closeCart()">Закрыть</button>
</div>
<div class="main_name_center">
<h1>SUGOi TOYS | Магазин оригинальных аниме фигурок</h1>
</div>
<main>
    
    <section class="slider container">
        <h2>Новинки</h2>
        <div class="slider">
            <div class="slides">
                <div class="slide"><img src="img/джунлиjpg.jpg" alt="Новинка 1"></div>
                <div class="slide"><img src="img/Демонесса Куйю.jpg" alt="Новинка 2"></div>
                <div class="slide"><img src="img/Мунечика.jpg" alt="Новинка 3"></div>
            </div>
            <div class="navigation">
                <button onclick="prevSlide()">&#10094;</button>
                <button onclick="nextSlide()">&#10095;</button>
            </div>
        </div>
    </section>

    <section class="products container">
        <h2>Наши товары</h2>
        <div class="filter">
            <!-- Фильтр будет здесь -->
        </div>
        <div class="product-list">
            <?php
            if ($result->num_rows > 0) {
                // Вывод первых 4 товаров
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product">';
                    echo '<img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                    echo '<p>Цена: ' . htmlspecialchars($row['price']) . ' руб.</p>';
                    echo '<button class="buy-button" onclick="window.location.href=\'tovars.php\'">Купить</button>';
                    echo '</div>';
                }
            } else {
                echo '<p>Товары не найдены.</p>';
            }
            ?>
        </div>
        <button class="view-all" onclick="window.location.href='tovars.php'">Посмотреть все товары</button>
    </section>
</main>

<footer>
    <p>&copy; 2024 Интернет-каталог товаров</p>
</footer>

<script>
    // Глобальная переменная для отслеживания количества товаров в корзине
    let cartCount = 0;

    function addToCart(button) {
        button.style.display = 'none'; // Скрыть кнопку "Добавить в корзину"
        const quantityControl = button.nextElementSibling;
        quantityControl.style.display = 'block'; // Показать управление количеством
        updateCart(1); // Увеличить количество в корзине
    }

    function increaseQuantity(button) {
        const quantityElement = button.previousElementSibling;
        let quantity = parseInt(quantityElement.textContent);
        quantity++;
        quantityElement.textContent = quantity;
        updateCart(1); // Увеличить количество в корзине
    }

    function decreaseQuantity(button) {
        const quantityElement = button.nextElementSibling;
        let quantity = parseInt(quantityElement.textContent);
        if (quantity > 1) {
            quantity--;
            quantityElement.textContent = quantity;
            updateCart(-1); // Уменьшить количество в корзине
        } else {
            button.parentElement.style.display = 'none'; // Скрыть управление количеством
            button.parentElement.previousElementSibling.style.display = 'block'; // Показать кнопку "Добавить в корзину"
            updateCart(-1); // Уменьшить количество в корзине
        }
    }

    function updateCart(change) {
        cartCount += change; // Обновить общее количество товаров в корзине
        const cartLink = document.querySelector('.cart-icon');
        // Если у вас есть текст для корзины, вы можете добавить его сюда
        // cartLink.textContent = `Корзина (${cartCount})`; // Убедитесь, что вы правильно обновляете текст
    }

    let currentSlide = 0;

    function showSlide(index) {
        const slides = document.querySelector('.slides');
        const totalSlides = document.querySelectorAll('.slide').length;

        if (index >= totalSlides) {
            currentSlide = 0;
        } else if (index < 0) {
            currentSlide = totalSlides - 1;
        } else {
            currentSlide = index;
        }

        slides.style.transform = `translateX(-${currentSlide * 100}%)`;
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    function prevSlide() {
        showSlide(currentSlide - 1);
    }

    // Автоматическая смена слайдов каждые 5 секунд
    setInterval(nextSlide, 5000);

    // Открытие и закрытие окна корзины
    function toggleCart() {
        const cartPopup = document.getElementById('cartPopup');
        const isVisible = cartPopup.style.display === 'block';

        if (isVisible) {
            cartPopup.style.display = 'none';
        } else {
            cartPopup.style.display = 'block';
        }
    }

    function closeCart() {
        document.getElementById('cartPopup').style.display = 'none';
    }
</script>
</body>
</html>
<?php
// Освобождение ресурсов
$result->close();
$mysqli->close();

if (isset($_SESSION['success_message'])) {
    echo "<script>alert('" . $_SESSION['success_message'] . "');</script>";
    unset($_SESSION['success_message']); // Удаляем сообщение после его отображения
}

if (isset($_SESSION['error_message'])) {
    echo "<script>alert('" . $_SESSION['error_message'] . "');</script>";
    unset($_SESSION['error_message']); // Удаляем сообщение после его отображения
}
?>
