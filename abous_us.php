<?php session_start(); // Начало сессии ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Наши товары</title>
    <link rel="stylesheet" href="assets/css/style.css"> 
</head>
<body>
<style>
        
        main {
    display: flex;
    flex-direction: column;
    align-items: center; /* Центрирование по горизонтали */
    text-align: center; /* Центрирование текста */
}

.promo-image {
    width: 80%; /* Измените ширину изображения по желанию */
    max-width: 800px; /* Максимальная ширина изображения */
    height: auto;
    margin-top: 20px; /* Отступ сверху */
}

.promo-text {
    font-size: 24px;
    margin-top: 20px;
    color: #A020F0; /* Цвет текста */
    text-shadow: 0.5px 0.5px 0 black, -0.5px -0.5px 0 black, 0.5px -0.5px 0 black, -0.5px 0.5px 0 black;

}

    </style>
<?php include 'header.php'; ?>





<main>
    <h1 style="text-align: center;">Оформление заказа</h1>
    
    <a href="https://vk.com/sugoitoys" target="_blank">
        <img src="img/1.jpg" alt="Загляните в нашу группу в ВК" class="promo-image"> 
    </a>
    
    <p class="promo-text">Загляните в нашу группу в ВК, будем рады!</p>
    <div class="quantity-control">
    <button class="" onclick="group_vk()">Группа вк</button>
    </div>
</main>









<footer>
    <p>&copy; 2024 Интернет-каталог товаров</p>
</footer>

<script src="assets/js/cart.js"></script>
<script>
    function group_vk() {
    // Открытие страницы оформления заказа в новой вкладке
    window.open('https://vk.com/sugoitoys', '_blank'); // Замените 'checkout.php' на нужную ссылку
}
</script>
