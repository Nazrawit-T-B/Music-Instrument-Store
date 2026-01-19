const searchInput = document.getElementById("search");
const resultsBox = document.getElementById("search-results");

searchInput.addEventListener("input", () => {
  const query = searchInput.value.toLowerCase().trim();
  resultsBox.innerHTML = "";

  if (query === "") return;

  const matches = productIndex.filter(product =>
    product.name.toLowerCase().includes(query)
  );

  matches.forEach(product => {
    const item = document.createElement("div");
    item.className = "search-result-item";
    item.textContent = product.name;

    item.addEventListener("click", () => {
      window.location.href =
        `productdetails.html#${product.targetId}`;
    });

    resultsBox.appendChild(item);
  });
});
