document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const orderForm = document.getElementById('orderForm');
    const successMessage = document.getElementById('successMessage');
    
    // Input fields
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const addressInput = document.getElementById('address');
    const productSelect = document.getElementById('product');
    const quantityInput = document.getElementById('quantity');
    
    // Error messages
    const nameError = document.getElementById('nameError');
    const emailError = document.getElementById('emailError');
    const phoneError = document.getElementById('phoneError');
    const addressError = document.getElementById('addressError');
    const productError = document.getElementById('productError');
    const quantityError = document.getElementById('quantityError');
    
    // Price display
    const totalPriceDisplay = document.getElementById('totalPrice');
    
    // Calculate and display total price when product or quantity changes
    productSelect.addEventListener('change', updateTotalPrice);
    quantityInput.addEventListener('input', updateTotalPrice);
    
    // Validate phone number format
    phoneInput.addEventListener('blur', validatePhone);
    
    // Form submission
    orderForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            // In a real application, you would send the data to a server here
            // For demo purposes, we'll just show the success message
            orderForm.style.display = 'none';
            successMessage.style.display = 'block';
            
            // Reset form after 5 seconds (for demo)
            setTimeout(() => {
                orderForm.reset();
                orderForm.style.display = 'block';
                successMessage.style.display = 'none';
                updateTotalPrice();
            }, 5000);
        }
    });
    
    // Validate the entire form
    function validateForm() {
        let isValid = true;
        
        // Validate name
        if (nameInput.value.trim() === '') {
            showError(nameInput, nameError);
            isValid = false;
        } else {
            hideError(nameInput, nameError);
        }
        
        // Validate email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value.trim())) {
            showError(emailInput, emailError);
            isValid = false;
        } else {
            hideError(emailInput, emailError);
        }
        
        // Validate phone (if provided)
        if (phoneInput.value.trim() !== '' && !validatePhone()) {
            isValid = false;
        }
        
        // Validate address
        if (addressInput.value.trim() === '') {
            showError(addressInput, addressError);
            isValid = false;
        } else {
            hideError(addressInput, addressError);
        }
        
        // Validate product
        if (productSelect.value === '') {
            showError(productSelect, productError);
            isValid = false;
        } else {
            hideError(productSelect, productError);
        }
        
        // Validate quantity
        if (quantityInput.value < 1 || isNaN(quantityInput.value)) {
            showError(quantityInput, quantityError);
            isValid = false;
        } else {
            hideError(quantityInput, quantityError);
        }
        
        return isValid;
    }
    
    // Validate phone number
    function validatePhone() {
        const phoneRegex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
        if (phoneInput.value.trim() !== '' && !phoneRegex.test(phoneInput.value.trim())) {
            showError(phoneInput, phoneError);
            return false;
        } else {
            hideError(phoneInput, phoneError);
            return true;
        }
    }
    
    // Show error message
    function showError(input, errorElement) {
        input.classList.add('error');
        errorElement.style.display = 'block';
    }
    
    // Hide error message
    function hideError(input, errorElement) {
        input.classList.remove('error');
        errorElement.style.display = 'none';
    }
    
    // Update total price display
    function updateTotalPrice() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        const total = (price * quantity).toFixed(2);
        
        totalPriceDisplay.textContent = `Total: $${total}`;
    }
    
    // Initialize total price
    updateTotalPrice();
});