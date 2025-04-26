// Product Form Submission
document.getElementById('addProductForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
    submitBtn.disabled = true;

    try {
        const formData = new FormData(this);
        formData.append('action', 'insert'); // (Optional) Add action identifier
        
        const response = await fetch('../PHP/insertFunction.php', {  // Updated to call insertFunction.php
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            // Show success message
            const successBanner = document.getElementById('successBanner');
            successBanner.style.display = 'block';
            successBanner.textContent = result.message;
            
            // Hide the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
            modal.hide();
            
            // Add the new product to the grid
            addProductToGrid(result.product);
            
            // Hide success message after 3 seconds
            setTimeout(() => {
                successBanner.style.display = 'none';
            }, 3000);
            
            // Reset the form
            this.reset();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message || 'Failed to add product',
                confirmButtonColor: '#3085d6'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while adding the product. Please try again.',
            confirmButtonColor: '#3085d6'
        });
    } finally {
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    }
});

// Function to add new product to the grid dynamically
function addProductToGrid(product) {
    const productGrid = document.getElementById('productGrid');

    const col = document.createElement('div');
    col.className = 'col-6 col-md-4 col-lg-3 product-card';
    col.dataset.productId = product.product_id;

    col.innerHTML = `
        <div class="card shadow-sm h-100">
            <img src="../${product.image_url || 'images/default-product.jpg'}" 
                class="card-img-top" 
                alt="${product.product_name || 'Product image'}">
            <div class="card-body">
                <h5 class="card-title">${product.product_name || 'Unknown Product'}</h5>
                <p class="card-text">₱${parseFloat(product.price).toFixed(2)}</p>
                <div class="d-flex justify-content-between">
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProductModal">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteProduct(${product.product_id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    `;

    // Insert before the "Add Product" card
    const addProductCard = document.getElementById('addProductCard');
    productGrid.insertBefore(col, addProductCard);
}

// Delete product placeholder function (for SweetAlert confirmation)
function deleteProduct(productId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover this product!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Just remove from DOM for now (you can enhance later with AJAX delete)
            const productCard = document.querySelector(`[data-product-id="${productId}"]`);
            if (productCard) {
                productCard.remove();
            }
            Swal.fire('Deleted!', 'The product has been deleted.', 'success');
        }
    });
}
