<?php
// Shared header component.
// Ensure session is active so auth-specific menu items work.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
  <nav class="container">
    <ul>
      <li><a href="index.php">Главная</a></li>
      <li><a href="tovars.php">Товары</a></li>
      <li><a href="abous_us.php">О нас</a></li>

      <!-- Корзина прижата вправо -->
      <li class="cart-item">
        <a href="#" onclick="toggleCart()">
          <span class="cart-icon">Корзина&nbsp;(0)</span>
        </a>
      </li>

      <?php if (isset($_SESSION['username'])): ?>
          <?php if (!empty($_SESSION['is_admin'])): ?>
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
<script>
  // Inline script to prevent cart count flicker
  (function() {
    const cart = JSON.parse(localStorage.getItem('cart')) || {};
    let totalItems = 0;
    for (const itemId in cart) {
        totalItems += cart[itemId].quantity;
    }
    const cartIcon = document.querySelector('.cart-icon');
    if (cartIcon) {
        cartIcon.textContent = `Корзина (${totalItems})`;
    }
  })();
</script>

<!-- Всплывающее окно корзины -->
<div class="cart-popup" id="cartPopup" style="display: none; color:black;">
    <h2>Корзина</h2>
    <div class="cart-items" id="cartItems">
        <!-- Список товаров в корзине будет здесь -->
    </div>
    <button onclick="purchase()">Купить</button>
    <button onclick="closeCart()">Закрыть</button>
</div>
