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
    <h1 style="text-align: center;">О нас</h1>
    <div class="about-us-container">
        <div class="schedule">
            <h2>График работы по московскому времени:</h2>
            <p>Понедельник - с 10:00 до 20:00</p>
            <p>Вторник - с 10:00 до 20:00</p>
            <p>Среда - с 10:00 до 20:00</p>
            <p>Четверг - с 10:00 до 20:00</p>
            <p>Пятница - с 10:00 до 20:00</p>
            <p>Суббота - выходной</p>
            <p>Воскресенье - выходной</p>
            <p>Праздничные дни - выходной</p>
        </div>
        <div class="contacts">
            <h2>Наши контакты:</h2>
            <p>Группа в ВК: <a href="https://vk.com/sugoitoys" target="_blank">https://vk.com/sugoitoys</a></p>
        </div>
    </div>
</main>









<?php include 'footer.php'; ?>

<script src="assets/js/cart.js"></script>
<script>
    // No script needed for this page anymore
</script>
