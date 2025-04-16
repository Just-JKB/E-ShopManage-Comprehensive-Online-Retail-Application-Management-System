document.addEventListener("DOMContentLoaded", () => {
  // Check if redirected after adding a product
  const urlParams = new URLSearchParams(window.location.search);
  const added = urlParams.get("added");

  if (added === "true") {
      const banner = document.getElementById("successBanner");
      if (banner) {
          banner.style.display = "block";

          // Hide banner after 3 seconds
          setTimeout(() => {
              banner.style.display = "none";
          }, 3000);
      }
  }

  // Load products from DB if needed
  fetchProducts();

  // Optional: hook up category buttons, sorting, etc.
});

async function fetchProducts() {
  try {
      const response = await fetch("../PHP/GetProducts.php"); // Make sure this PHP file exists
      const products = await response.json();

      const productGrid = document.getElementById("productGrid");

      // Clear current products except the Add Product card
      productGrid.innerHTML = document.getElementById("addProductCard").outerHTML;

      products.forEach(product => {
          const productCard = document.createElement("div");
          productCard.className = "col-6 col-md-4 col-lg-3";
          productCard.innerHTML = `
              <div class="card h-100 shadow-sm">
                  <img src="${product.image_url}" class="card-img-top" alt="${product.name}">
                  <div class="card-body">
                      <h5 class="card-title">${product.name}</h5>
                      <p class="card-text">${product.description}</p>
                      <p class="card-text"><strong>₱${product.price}</strong></p>
                      <span class="badge bg-secondary">${product.category}</span>
                  </div>
              </div>
          `;
          productGrid.appendChild(productCard);
      });

  } catch (error) {
      console.error("Failed to fetch products:", error);
  }
}

// Add product form handler
document.getElementById("addProductForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  try {
      const response = await fetch("../PHP/Inventory.php", {
          method: "POST",
          body: formData
      });

      const result = await response.json();

      if (result.success) {
          Swal.fire({
              title: "Success",
              text: "Product added successfully!",
              icon: "success",
              timer: 1500,
              showConfirmButton: false
          }).then(() => {
              window.location.href = "Inventory.html?added=true";
          });
      } else {
          Swal.fire("Error", result.message || "Failed to add product", "error");
      }

  } catch (error) {
      console.error("Fetch error:", error);
      Swal.fire("Error", "An error occurred", "error");
  }
});
