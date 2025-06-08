<?php
session_start();

// Подключение к базе данных
include 'db_connection.php'; // Используем централизованное подключение

// Проверка на наличие ошибок подключения
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

// Получение всех категорий для фильтрации
$categoryQuery = "SELECT * FROM categories";
$categoryResult = $mysqli->query($categoryQuery);

// Получение всех товаров из базы данных
$productQuery = "SELECT * FROM products";
$productResult = $mysqli->query($productQuery);

// Получение всех товаров с фильтрацией
$filteredProducts = [];
if ($productResult->num_rows > 0) {
    while ($row = $productResult->fetch_assoc()) {
        $filteredProducts[] = $row;
    }
}

// Проверка, если параметры фильтрации переданы
if (isset($_GET['category_id']) || isset($_GET['max_price']) || isset($_GET['search'])) {
    $categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : '';
    $maxPrice = isset($_GET['max_price']) ? $_GET['max_price'] : '';
    $searchTerm = isset($_GET['search']) ? strtolower($_GET['search']) : '';

    // Фильтрация товаров
    $filteredProducts = array_filter($filteredProducts, function($product) use ($categoryId, $maxPrice, $searchTerm) {
        $matchesCategory = $categoryId === "" || $product['category_id'] == $categoryId;
        $matchesPrice = $maxPrice === "" || $product['price'] <= $maxPrice;
        $matchesSearch = strpos(strtolower($product['name']), $searchTerm) !== false;

        return $matchesCategory && $matchesPrice && $matchesSearch;
    });
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Наши товары</title>
    <link rel="stylesheet" href="assets/css/style.css"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>

<header>
    <nav class="container">
        <ul>
            <li><a href="index.php">Главная</a></li>
            <li><a href="tovars.php">Товары</a></li>
            <li><a href="abous_us.php">О нас</a></li>
            <li>
                <a href="#" onclick="toggleCart()">
                    <span class="cart-icon">🛒</span>
                </a>
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
<div class="cart-popup" id="cartPopup" style="display: none; color:black;">
    <h2>Корзина</h2>
    <div class="cart-items" id="cartItems">
        <!-- Список товаров в корзине будет здесь -->
    </div>
    <button onclick="purchase()">Купить</button> <!-- Кнопка "Купить" -->
    <button onclick="closeCart()">Закрыть</button>
</div>

<main>
    <h1 style="text-align: center;">Наши товары</h1>

    <div class="filter-section">
        <div class="filter-group">
            <label for="category">Категория:</label>
            <select id="category" onchange="filterProducts()">
                <option value="">Все</option>
                <?php while ($category = $categoryResult->fetch_assoc()): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="filter-group">
            <label for="price">Цена:</label>
            <input type="number" id="price" placeholder="Максимальная цена" onchange="filterProducts()">
        </div>
        <div class="filter-group">
            <label for="search">Поиск:</label>
            <input type="text" id="search" placeholder="Название товара" oninput="filterProducts()">
        </div>
    </div>

    <div class="product-list" id="productList">
        <?php if (empty($filteredProducts)): ?>
            <p>Нет товаров, соответствующих вашим критериям.</p>
        <?php else: ?>
            <?php foreach ($filteredProducts as $product): ?>
                <div class="product" data-category-id="<?php echo $product['category_id']; ?>" data-price="<?php echo $product['price']; ?>" data-id="<?php echo $product['id']; ?>">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <h3><?php echo $product['name']; ?></h3>
                    <p><?php echo $product['description']; ?></p>
                    <p>Цена: <?php echo $product['price']; ?> руб.</p>
                    <button class="add-to-cart" onclick="addToCart(this)">Добавить в корзину</button>
                    <div class="quantity-control" style="display: none;">
                        <button onclick="decreaseQuantity(this)">-</button>
                        <span class="quantity">1</span>
                        <button onclick="increaseQuantity(this)">+</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2024 Интернет-каталог товаров</p>
</footer>

<script>
    // Глобальная переменная для отслеживания товаров в корзине
    let cartItems = {};

    function filterProducts() {
    const categoryId = $('#category').val();
    const maxPrice = $('#price').val();
    const searchTerm = $('#search').val();

    $.ajax({
        url: 'filter_products.php', // файл, который будет обрабатывать запрос
        method: 'GET',
        data: {
            category_id: categoryId,
            max_price: maxPrice,
            search: searchTerm
        },
        success: function(data) {
            $('#productList').html(data); // Обновление списка товаров
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Ошибка AJAX: ", textStatus, errorThrown);
        }
    });
}


    function addToCart(button) {
        const productElement = button.closest('.product');
        const productId = productElement.getAttribute('data-id');
        const productName = productElement.querySelector('h3').textContent;
        const productPrice = productElement.getAttribute('data-price');
        const quantityControl = button.nextElementSibling;

        button.style.display = 'none'; // Скрыть кнопку "Добавить в корзину"
        quantityControl.style.display = 'block'; // Показать управление количеством

        // Проверка наличия товара в корзине
        if (!cartItems[productId]) {
            cartItems[productId] = {
                name: productName,
                price: productPrice,
                quantity: 1
            };
        } else {
            cartItems[productId].quantity++;
        }

        console.log("Добавлен товар в корзину: ", cartItems[productId]); // Отладочная информация
        updateCart();
    }

    function increaseQuantity(button) {
        const quantityElement = button.previousElementSibling;
        let quantity = parseInt(quantityElement.textContent);
        quantity++;
        quantityElement.textContent = quantity;

        const productElement = button.closest('.product');
        const productId = productElement.getAttribute('data-id');
        cartItems[productId].quantity++;

        console.log("Увеличено количество товара в корзине: ", cartItems[productId]); // Отладочная информация
        updateCart();
    }

    function decreaseQuantity(button) {
        const quantityElement = button.nextElementSibling;
        let quantity = parseInt(quantityElement.textContent);
        const productElement = button.closest('.product');
        const productId = productElement.getAttribute('data-id');

        if (quantity > 1) {
            quantity--;
            quantityElement.textContent = quantity;
            cartItems[productId].quantity--;

            console.log("Уменьшено количество товара в корзине: ", cartItems[productId]); // Отладочная информация
            updateCart();
        } else {
            button.parentElement.style.display = 'none'; // Скрыть управление количеством
            button.parentElement.previousElementSibling.style.display = 'block'; // Показать кнопку "Добавить в корзину"
            delete cartItems[productId]; // Удалить товар из корзины
            console.log("Товар удален из корзины: ", productId); // Отладочная информация
            updateCart();
        }
    }

    function updateCart() {
        const cartItemsContainer = document.getElementById('cartItems');
        cartItemsContainer.innerHTML = ''; // Очистить текущее содержимое корзины

        let totalItems = 0;

        // Проверяем, есть ли товары в корзине
        if (Object.keys(cartItems).length === 0) {
            cartItemsContainer.innerHTML = '<p>Корзина пуста</p>'; // Сообщение о пустой корзине
        } else {
            for (const itemId in cartItems) {
                const item = cartItems[itemId];
                totalItems += item.quantity;
                cartItemsContainer.innerHTML += `
                    <div class="cart-item">
                        <p>${item.name} - ${item.quantity} шт. по ${item.price} руб.</p>
                    </div>
                `;
            }
        }

        const cartLink = document.querySelector('.cart-icon');
        cartLink.textContent = `Корзина (${totalItems})`; // Обновление текста корзины
    }

    // Открытие и закрытие окна корзины
    function toggleCart() {
        const cartPopup = document.getElementById('cartPopup');
        const isVisible = cartPopup.style.display === 'block';

        if (isVisible) {
            cartPopup.style.display = 'none';
        } else {
            cartPopup.style.display = 'block';
            updateCart(); // Обновление содержимого корзины при открытии
        }
    }

    function closeCart() {
        document.getElementById('cartPopup').style.display = 'none';
    }

    function purchase() {
        window.open('https://vk.com/im?media=&sel=-213494634', '_blank'); 
    }
</script>

</body>
</html>
