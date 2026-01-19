// script.js for cart.html
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.cart-table')) {
        loadCart();
    } else if (document.querySelector('.order-summary')) {
        loadOrderSummary();
    }
});

function loadCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const tbody = document.querySelector('.cart-table tbody');
    tbody.innerHTML = ''; // Clear existing rows

    let subtotal = 0;

    cart.forEach((item, index) => {
        const total = item.price * item.quantity;
        subtotal += total;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div class="product-info">
                    <img src="${item.image}" alt="${item.title}" style="width: 50px; height: 50px;">
                    <span>${item.title}</span>
                </div>
            </td>
            <td>
                <button onclick="changeQuantity(${index}, -1)">-</button>
                <span>${item.quantity}</span>
                <button onclick="changeQuantity(${index}, 1)">+</button>
            </td>
            <td>
                <button onclick="removeItem(${index})">Remove</button>
            </td>
            <td>$${total.toFixed(2)}</td>
        `;
        tbody.appendChild(row);
    });

    // Update totals
    const tax = subtotal * 0.17; // 17% tax
    const discount = 0; // No discount
    const total = subtotal + tax - discount;

    document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('tax').textContent = `$${tax.toFixed(2)}`;
    document.getElementById('discount').textContent = `-$${discount.toFixed(2)}`;
    document.getElementById('total').textContent = `$${total.toFixed(2)}`;
}

function changeQuantity(index, delta) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart[index]) {
        cart[index].quantity += delta;
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
    }
}

function removeItem(index) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    loadCart();
}



function loadOrderSummary() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const orderItems = document.getElementById('order-items');
    orderItems.innerHTML = '';

    let subtotal = 0;

    cart.forEach(item => {
        const total = item.price * item.quantity;
        subtotal += total;

        const itemDiv = document.createElement('div');
        itemDiv.innerHTML = `
            <p>${item.title} x ${item.quantity} - $${total.toFixed(2)}</p>
        `;
        orderItems.appendChild(itemDiv);
    });

    const tax = subtotal * 0.17;
    const discount = 0;
    const total = subtotal + tax - discount;

    document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('tax').textContent = `$${tax.toFixed(2)}`;
    document.getElementById('discount').textContent = `-$${discount.toFixed(2)}`;
    document.getElementById('total').textContent = `$${total.toFixed(2)}`;
}

function placeorder() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    if(cart.length === 0){
        alert("Cart is empty!");
        return;
    }
    alert("Order has been placed successfully!");
    localStorage.removeItem("cart");
    window.location.href="index.html";
}


