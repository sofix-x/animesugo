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

<?php include 'header.php'; ?>


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

<script src="assets/js/cart.js"></script>
<script>
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
                // После обновления списка товаров, нужно заново инициализировать кнопки
                initializeProductButtons();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Ошибка AJAX: ", textStatus, errorThrown);
            }
        });
    }
</script>

</body>
</html>
