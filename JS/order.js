// Open the order modal when a product is ordered
function orderNow(productId, productName, price) {
    document.getElementById('modalProductId').value = productId;
    document.getElementById('modalProductName').value = productName;
    document.getElementById('modalProductPrice').value = price;
    document.getElementById('orderModal').style.display = 'flex';
}

// Close the order modal
function closeModal() {
    document.getElementById('orderModal').style.display = 'none';
}

// Open the review modal and show the reviews for the product
function openReviewModal(productId) {
    // Simulated reviews – replace this with AJAX fetch from PHP/DB
    const sampleReviews = {
        1: ["Great quality!", "Worth the price."],
        2: ["Fast delivery!", "Very useful product."],
        3: ["Not what I expected.", "Customer service helped though."],
    };

    const reviews = sampleReviews[productId] || ["No reviews yet."];
    const reviewHTML = reviews.map(review => `<div class="review-item">${review}</div>`).join('');

    document.getElementById('reviewList').innerHTML = reviewHTML;
    document.getElementById('reviewModal').style.display = 'flex';
}

// Close the review modal
function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
}

// Handle the order form submission
document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const productId = parseInt(document.getElementById('modalProductId').value);
    const productName = document.getElementById('modalProductName').value;
    const price = parseFloat(document.getElementById('modalProductPrice').value);
    const quantity = parseInt(document.getElementById('quantity').value);
    const paymentMethod = document.getElementById('paymentMethod').value;

    if (!paymentMethod || quantity < 1) {
        alert("Please fill all fields correctly.");
        return;
    }

    const total = price * quantity;

    const orderData = {
        user_id: 1, // Replace this with session user ID dynamically
        total_price: total,
        payment_method: paymentMethod,
        order_items: [
            {
                product_id: productId,
                quantity: quantity,
                price: price
            }
        ]
    };

    fetch('PlaceOrder.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        closeModal();
        if (data.success) {
            alert(`${productName} ordered successfully! Order ID: ${data.order_id}`);
            window.location.href = 'OrderDashboard.php';
        } else {
            alert('Order failed: ' + data.error);
        }
    })
    .catch(error => {
        closeModal();
        alert('Order error: ' + error);
    });
});
