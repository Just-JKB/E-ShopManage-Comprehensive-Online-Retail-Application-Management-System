document.addEventListener('DOMContentLoaded', function() {
  // Product Form Submission
  document.getElementById('addProductForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
      submitBtn.disabled = true;

      try {
          const formData = new FormData(this);
          
          const response = await fetch(this.action, {
              method: this.method,
              body: formData
          });

          if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);  // Fixed string interpolation
          }

          const result = await response.json();
          console.log('Server response:', result);

          if (result.success) {
              // Show success SweetAlert
              await Swal.fire({
                  icon: 'success',
                  title: 'Product Added!',
                  text: result.message || 'The product was successfully added.',
                  confirmButtonColor: '#28a745', // green button
                  confirmButtonText: 'OK'
              });

              // Hide modal and reset form
              const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
              if (modal) modal.hide();
              this.reset();

              // Add product to grid
              if (result.product && result.product.product_id) {
                  addProductToGrid(result.product);
              }
          } else {
              // Show error SweetAlert
              await Swal.fire({
                  icon: 'error',
                  title: 'Failed to Add Product',
                  text: result.message || 'Please try again.',
                  confirmButtonColor: '#dc3545', // red button
                  confirmButtonText: 'OK'
              });
          }
      } catch (error) {
          console.error('Error:', error);
          Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: error.message || 'An unexpected error occurred. Please try again.',
              confirmButtonColor: '#dc3545',
              confirmButtonText: 'OK'
          });
      } finally {
          submitBtn.innerHTML = originalBtnText;
          submitBtn.disabled = false;
      }
  });
});

// Function to add new product to the grid dynamically
function addProductToGrid(product) {
  const productGrid = document.getElementById('productGrid');
  
  // Create image URL - handle backslashes for Windows paths
  const imageUrl = product.image_url 
      ? '../' + product.image_url.replace(/\\/g, '/') 
      : '../images/default-product.jpg';

  const col = document.createElement('div');
  col.className = 'col-6 col-md-4 col-lg-3 product-card';
  col.dataset.productId = product.product_id;

  col.innerHTML = ` 
      <div class="card shadow-sm h-100">
          <img src="${imageUrl}" 
              class="card-img-top" 
              alt="${product.product_name || 'Product image'}">
          <div class="card-body">
              <h5 class="card-title">${product.product_name || 'Unknown Product'}</h5>
              <p class="card-text">₱${parseFloat(product.price || 0).toFixed(2)}</p>
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

  const addProductCard = document.getElementById('addProductCard');
  productGrid.insertBefore(col, addProductCard);
}

// Delete product function
// Delete product function
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
          const productCard = document.querySelector(`[data-product-id="${productId}"]`);  // Fixed query selector
          if (productCard) {
              productCard.remove();
          }
          Swal.fire('Deleted!', 'The product has been deleted.', 'success');
      }
  });
}
