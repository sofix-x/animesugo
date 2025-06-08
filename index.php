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

<?php include 'header.php'; ?>

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

<script src="assets/js/cart.js"></script>
<script>
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
