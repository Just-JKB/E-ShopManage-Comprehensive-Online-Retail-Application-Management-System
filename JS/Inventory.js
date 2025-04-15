form.addEventListener('submit', function (e) {
  e.preventDefault();

  const name = document.getElementById('productName').value;
  const img = document.getElementById('productImage').value;
  const category = document.getElementById('productCategory').value;

  const newCard = document.createElement('div');
  newCard.className = 'col-6 col-md-4 col-lg-3 product-item';
  newCard.setAttribute('data-category', category);
  newCard.innerHTML = `
    <div class="border rounded p-3 bg-white shadow-sm h-100 d-flex flex-column">
      <img src="${img}" alt="${name}" class="img-fluid mb-2 rounded" style="height:180px; object-fit:cover;">
      <h6 class="mb-1">${name}</h6>
      <p class="text-muted small mb-2">Size: M | Color: Black</p>
      <p class="mb-2 fw-bold">$39.99</p>
      <div class="mt-auto d-flex justify-content-between">
        <button class="btn btn-sm btn-outline-primary edit-btn" data-id="new" data-bs-toggle="modal" data-bs-target="#editProductModal">Edit</button>
        <button class="btn btn-sm btn-outline-danger delete-btn" data-id="new">Delete</button>
      </div>
    </div>
  `;

  productGrid.insertBefore(newCard, addCard);
  form.reset();

  const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
  modal.hide();
  applyFilters();

  Swal.fire({
    icon: 'success',
    title: 'Product Added!',
    text: `"${name}" has been successfully added.`,
    showConfirmButton: false,
    timer: 2000
  });
});
