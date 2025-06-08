document.addEventListener('DOMContentLoaded', () => {
    // Инициализация корзины из localStorage при загрузке страницы
    updateCart();
    initializeProductButtons();
});

// Получение корзины из localStorage
function getCart() {
    return JSON.parse(localStorage.getItem('cart')) || {};
}

// Сохранение корзины в localStorage
function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCart();
}

// Обновление отображения корзины
function updateCart() {
    const cart = getCart();
    const cartItemsContainer = document.getElementById('cartItems');
    const cartIcon = document.querySelector('.cart-icon');
    let totalItems = 0;

    if (cartItemsContainer) {
        cartItemsContainer.innerHTML = '';
        if (Object.keys(cart).length === 0) {
            cartItemsContainer.innerHTML = '<p>Корзина пуста</p>';
        } else {
            for (const itemId in cart) {
                const item = cart[itemId];
                totalItems += item.quantity;
                cartItemsContainer.innerHTML += `
                    <div class="cart-item" data-id="${itemId}">
                        <p>${item.name} - ${item.quantity} шт. по ${item.price} руб.</p>
                    </div>
                `;
            }
        }
    }

    if (cartIcon) {
        cartIcon.textContent = `Корзина (${totalItems})`;
    }
    
    // Обновляем состояние кнопок на странице товаров
    initializeProductButtons();
}

// Добавление товара в корзину
function addToCart(button) {
    const productElement = button.closest('.product');
    const productId = productElement.getAttribute('data-id');
    const productName = productElement.querySelector('h3').textContent;
    const productPrice = productElement.getAttribute('data-price');
    
    const cart = getCart();

    if (!cart[productId]) {
        cart[productId] = {
            name: productName,
            price: productPrice,
            quantity: 1
        };
    } else {
        cart[productId].quantity++;
    }

    saveCart(cart);
    
    // Обновляем вид кнопки
    button.style.display = 'none';
    const quantityControl = button.nextElementSibling;
    quantityControl.style.display = 'block';
    quantityControl.querySelector('.quantity').textContent = cart[productId].quantity;
}

// Увеличение количества товара
function increaseQuantity(button) {
    const productElement = button.closest('.product');
    const productId = productElement.getAttribute('data-id');
    const cart = getCart();

    if (cart[productId]) {
        cart[productId].quantity++;
        saveCart(cart);
        button.previousElementSibling.textContent = cart[productId].quantity;
    }
}

// Уменьшение количества товара
function decreaseQuantity(button) {
    const productElement = button.closest('.product');
    const productId = productElement.getAttribute('data-id');
    const cart = getCart();

    if (cart[productId]) {
        cart[productId].quantity--;
        if (cart[productId].quantity <= 0) {
            delete cart[productId];
            // Показать кнопку "Добавить в корзину"
            const quantityControl = button.parentElement;
            quantityControl.style.display = 'none';
            quantityControl.previousElementSibling.style.display = 'block';
        } else {
            button.nextElementSibling.textContent = cart[productId].quantity;
        }
        saveCart(cart);
    }
}

// Инициализация кнопок товаров
function initializeProductButtons() {
    const cart = getCart();
    const productElements = document.querySelectorAll('.product');

    productElements.forEach(productElement => {
        const productId = productElement.getAttribute('data-id');
        const addToCartButton = productElement.querySelector('.add-to-cart');
        const quantityControl = productElement.querySelector('.quantity-control');
        const quantitySpan = productElement.querySelector('.quantity');

        if (cart[productId]) {
            addToCartButton.style.display = 'none';
            quantityControl.style.display = 'block';
            quantitySpan.textContent = cart[productId].quantity;
        } else {
            addToCartButton.style.display = 'block';
            quantityControl.style.display = 'none';
        }
    });
}


// Открытие и закрытие окна корзины
function toggleCart() {
    const cartPopup = document.getElementById('cartPopup');
    if (cartPopup) {
        const isVisible = cartPopup.style.display === 'block';
        cartPopup.style.display = isVisible ? 'none' : 'block';
        if (!isVisible) {
            updateCart();
        }
    }
}

function closeCart() {
    const cartPopup = document.getElementById('cartPopup');
    if (cartPopup) {
        cartPopup.style.display = 'none';
    }
}

function purchase() {
    // Очищаем корзину после "покупки"
    saveCart({});
    // Перенаправляем пользователя
    window.open('https://vk.com/im?media=&sel=-213494634', '_blank');
    // Закрываем окно корзины
    closeCart();
}
