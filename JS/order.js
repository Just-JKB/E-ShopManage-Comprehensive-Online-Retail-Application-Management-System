// order.js

// Function to open the order modal with product details
function orderNow(productId, productName, productPrice) {
    document.getElementById('modalProductId').value = productId;
    document.getElementById('modalProductName').value = productName;
    document.getElementById('modalProductPrice').value = productPrice;
    document.getElementById('orderModal').style.display = 'block';
}

// Function to close the order modal
function closeModal() {
    document.getElementById('orderModal').style.display = 'none';
    document.getElementById('orderForm').reset();
}

// Handle form submission
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("orderForm");

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const productId = document.getElementById("modalProductId").value;
        const quantity = document.getElementById("quantity").value;
        const paymentMethod = document.getElementById("paymentMethod").value;

        if (!paymentMethod) {
            alert("Please select a payment method.");
            return;
        }

        const formData = new FormData();
        formData.append("product_id", productId);
        formData.append("quantity", quantity);
        formData.append("payment_method", paymentMethod);

        fetch("placeOrder.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("✅ Order placed successfully!");
                closeModal();
            } else {
                alert("❌ Order failed: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("❌ An error occurred while placing the order.");
        });
    });
});
