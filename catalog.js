const products = [
    {
      name: "Nike Air Zoom",
      season: "vara",
      style: "sport",
      brand: "Nike",
      image: "assets/nike.jpg"
    },
    {
      name: "Dr. Martens Classic",
      season: "iarna",
      style: "casual",
      brand: "Dr. Martens",
      image: "assets/martens.jpg"
    },
    {
      name: "Adidas Samba",
      season: "primavara",
      style: "casual",
      brand: "Adidas",
      image: "assets/adidas.jpg"
    },
    {
      name: "Pantofi eleganți cu toc",
      season: "toamna",
      style: "elegant",
      brand: "Dr. Martens",
      image: "assets/heels.jpg"
    }
  ];
  
  const productList = document.getElementById("product-list");
  const searchInput = document.getElementById("search");
  const seasonFilter = document.getElementById("season-filter");
  const styleFilter = document.getElementById("style-filter");
  const brandFilter = document.getElementById("brand-filter");
  
  function renderProducts(list) {
    productList.innerHTML = "";
    list.forEach(product => {
      const card = document.createElement("div");
      card.className = "card";
      card.innerHTML = `
        <img src="${product.image}" alt="${product.name}" />
        <h4>${product.name}</h4>
        <p>Sezon: ${product.season}</p>
        <p>Stil: ${product.style}</p>
        <p>Marcă: ${product.brand}</p>
      `;
      productList.appendChild(card);
    });
  }
  
  function applyFilters() {
    const query = searchInput.value.toLowerCase();
    const season = seasonFilter.value;
    const style = styleFilter.value;
    const brand = brandFilter.value;
  
    const filtered = products.filter(p =>
      p.name.toLowerCase().includes(query) &&
      (season === "" || p.season === season) &&
      (style === "" || p.style === style) &&
      (brand === "" || p.brand === brand)
    );
  
    renderProducts(filtered);
  }
  
  searchInput.addEventListener("input", applyFilters);
  seasonFilter.addEventListener("change", applyFilters);
  styleFilter.addEventListener("change", applyFilters);
  brandFilter.addEventListener("change", applyFilters);
  
  // Initial render
  renderProducts(products);
  