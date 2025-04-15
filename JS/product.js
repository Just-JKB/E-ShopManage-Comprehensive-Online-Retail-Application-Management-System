document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('addProductForm');
  const productGrid = document.getElementById('productGrid');
  const addCard = document.getElementById('addProductCard');
  const searchInput = document.getElementById('productSearch');
  const categoryButtons = document.querySelectorAll('.category-btn');

  let currentCategory = 'all';

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const name = document.getElementById('productName').value;
    const img = document.getElementById('productImage').value;
    const category = document.getElementById('productCategory').value;

    const newCard = document.createElement('div');
    newCard.className = 'col-6 col-md-4 col-lg-3 product-item';
    newCard.setAttribute('data-category', category);
    newCard.innerHTML = `
      <div class="product-card">
        <img src="${img}" alt="Product Image" class="rounded shadow-sm w-100">
        <div class="product-name mt-2">${name}</div>
      </div>
    `;

    productGrid.insertBefore(newCard, addCard);
    form.reset();

    const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
    modal.hide();
    applyFilters();
  });

  function applyFilters() {
    const searchText = searchInput.value.toLowerCase();
    const productItems = productGrid.querySelectorAll('.product-item');

    productItems.forEach(item => {
      const name = item.querySelector('.product-name').textContent.toLowerCase();
      const category = item.getAttribute('data-category');

      const matchesCategory = currentCategory === 'all' || category === currentCategory;
      const matchesSearch = name.includes(searchText);

      item.style.display = matchesCategory && matchesSearch ? 'block' : 'none';
    });
  }

  searchInput.addEventListener('input', applyFilters);

  categoryButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      currentCategory = this.getAttribute('data-category');
      applyFilters();
    });
  });

  
});
