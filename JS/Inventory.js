document.addEventListener("DOMContentLoaded", () => {
    const filterSelect = document.getElementById("filterSelect");
    const productContainer = document.getElementById("productContainer");
  
    // Fetch products from the server
    fetch("../PHP/Inventory.php")
      .then((res) => res.json())
      .then((data) => {
        // Initially display all products
        displayProducts(data);
  
        // Listen for category filter change
        filterSelect.addEventListener("change", (e) => {
          const selectedCategory = e.target.value;
          const filteredProducts = selectedCategory === "all"
            ? data
            : data.filter((product) => product.category.toLowerCase() === selectedCategory.toLowerCase());
          displayProducts(filteredProducts);
        });
      })
      .catch((err) => {
        console.error("Failed to fetch products", err);
      });
  
    // Function to display products
    function displayProducts(products) {
      productContainer.innerHTML = ""; // Clear existing products
  
      // Loop through the filtered or all products and create product cards
      products.forEach((product) => {
        const card = document.createElement("div");
        card.className = "col-6 col-md-4 col-lg-3 product-card";
        card.innerHTML = `
          <div class="card shadow-sm h-100">
            <img src="${product.image_url}" class="card-img-top" alt="${product.name}">
            <div class="card-body">
              <h5 class="card-title">${product.name}</h5>
              <p class="card-text">${product.description}</p>
              <div class="d-flex justify-content-between">
                <button class="btn btn-warning btn-sm">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="deleteProduct(${product.id})">Delete</button>
              </div>
            </div>
          </div>
        `;
        productContainer.appendChild(card);
      });
    }
  
    // Function to delete a product (just a placeholder for now)
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
          // Handle product deletion logic (you can send a delete request to your server here)
          Swal.fire('Deleted!', 'The product has been deleted.', 'success');
        }
      });
    }
  });
  