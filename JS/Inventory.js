const searchName = document.getElementById("searchName");
const categorySelect = document.getElementById("categorySelect");
const table = document.getElementById("inventoryTable");
const rows = table.getElementsByTagName("tr");

searchName.addEventListener("input", filterTable);
categorySelect.addEventListener("change", filterTable);

function filterTable() {
  const nameFilter = searchName.value.toLowerCase();
  const categoryFilter = categorySelect.value.toLowerCase();

  for (let i = 1; i < rows.length; i++) {
    const nameCell = rows[i].getElementsByTagName("td")[0];
    const categoryCell = rows[i].getElementsByTagName("td")[4];

    const nameText = nameCell.textContent.toLowerCase();
    const categoryText = categoryCell.textContent.toLowerCase();

    const nameMatch = nameText.includes(nameFilter);
    const categoryMatch = categoryFilter === "" || categoryText.includes(categoryFilter);

    rows[i].style.display = nameMatch && categoryMatch ? "" : "none";
  }
}
