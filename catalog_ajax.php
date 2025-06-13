<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartFoot - Catalog</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            justify-content: center;
            background-color: #f4f4f4;
            padding: 1.5rem;
            border-radius: 12px;
        }
        
        .filters select, .filters input {
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            min-width: 150px;
        }
        
        .loading {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        
        .error {
            text-align: center;
            padding: 2rem;
            color: #e74c3c;
            background: #fdf0ed;
            border-radius: 8px;
            margin: 1rem 0;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        
        .pagination button {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            background: white;
            cursor: pointer;
            border-radius: 4px;
        }
        
        .pagination button:hover {
            background: #f0f0f0;
        }
        
        .pagination button.active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }
        
        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .product-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            overflow: hidden;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .product-info {
            padding: 1.5rem;
        }
        
        .product-name {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        
        .product-brand {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }
        
        .product-description {
            color: #444;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.4;
        }
        
        .product-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .product-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .product-rating {
            color: #f39c12;
            font-weight: bold;
        }
        
        .product-meta {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }
        
        .product-meta span {
            background: #f8f9fa;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.85rem;
            color: #666;
        }
    </style>
</head>
<body>
    <header>
        <h1>SmartFoot</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="catalog_ajax.php" class="active">Catalog</a>
            <a href="recommendations.php">Recomandări</a>
            <a href="statistics.php">Statistici</a>
            <a href="logout.php">Deconectare</a>
        </nav>
    </header>

    <main>
        <h2>Catalogul nostru de încălțăminte</h2>
        
        <!-- Filtre -->
        <div class="filters">
            <select id="brandFilter">
                <option value="">Toate mărcile</option>
                <option value="Nike">Nike</option>
                <option value="Adidas">Adidas</option>
                <option value="Puma">Puma</option>
                <option value="Converse">Converse</option>
            </select>
            
            <select id="styleFilter">
                <option value="">Toate stilurile</option>
                <option value="sneakers">Sneakers</option>
                <option value="cizme">Cizme</option>
                <option value="sandale">Sandale</option>
                <option value="papuci">Papuci</option>
            </select>
            
            <select id="occasionFilter">
                <option value="">Toate ocaziile</option>
                <option value="sport">Sport</option>
                <option value="casual">Casual</option>
                <option value="elegant">Elegant</option>
            </select>
            
            <button id="applyFilters">Aplică filtrele</button>
            <button id="clearFilters">Resetează</button>
        </div>
        
        <!-- Container pentru produse -->
        <div id="productsContainer">
            <div class="loading">Se încarcă produsele...</div>
        </div>
        
        <!-- Paginare -->
        <div id="paginationContainer"></div>
    </main>

    <footer>
        <p>&copy; 2025 SmartFoot. Toate drepturile rezervate.</p>
    </footer>

    <script>
        // Starea aplicației
        let currentPage = 1;
        let totalPages = 1;
        let isLoading = false;
        
        // Referințe la elemente
        const productsContainer = document.getElementById('productsContainer');
        const paginationContainer = document.getElementById('paginationContainer');
        const brandFilter = document.getElementById('brandFilter');
        const styleFilter = document.getElementById('styleFilter');
        const occasionFilter = document.getElementById('occasionFilter');
        const applyFiltersBtn = document.getElementById('applyFilters');
        const clearFiltersBtn = document.getElementById('clearFilters');
        
        /**
         * Încarcă produsele de pe server
         */
        async function loadProducts(page = 1, filters = {}) {
            if (isLoading) return;
            
            isLoading = true;
            productsContainer.innerHTML = '<div class="loading">Se încarcă produsele...</div>';
            
            try {
                // Construiește URL-ul cu parametrii
                const params = new URLSearchParams({
                    page: page,
                    limit: 12,
                    ...filters
                });
                
                const response = await fetch(`api.php?action=catalog&${params}`);
                const result = await response.json();
                
                if (result.success) {
                    displayProducts(result.data.products);
                    setupPagination(result.data.pagination);
                    currentPage = result.data.pagination.page;
                    totalPages = result.data.pagination.pages;
                } else {
                    showError(result.message || 'Eroare la încărcarea produselor');
                }
                
            } catch (error) {
                console.error('Eroare de rețea:', error);
                showError('Eroare de conexiune. Verificați internetul și încercați din nou.');
            } finally {
                isLoading = false;
            }
        }
        
        /**
         * Afișează produsele în grid
         */
        function displayProducts(products) {
            if (!products || products.length === 0) {
                productsContainer.innerHTML = '<div class="error">Nu s-au găsit produse pentru filtrele selectate.</div>';
                return;
            }
            
            const grid = document.createElement('div');
            grid.className = 'product-grid';
            
            products.forEach(product => {
                const card = createProductCard(product);
                grid.appendChild(card);
            });
            
            productsContainer.innerHTML = '';
            productsContainer.appendChild(grid);
        }
        
        /**
         * Creează cardul pentru un produs
         */
        function createProductCard(product) {
            const card = document.createElement('div');
            card.className = 'product-card';
            
            // Imaginea cu fallback
            const imageUrl = product.image_url || 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400';
            
            card.innerHTML = `
                <img src="${imageUrl}" 
                     alt="${product.name}" 
                     class="product-image"
                     onerror="this.src='https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400'">
                <div class="product-info">
                    <h3 class="product-name">${product.name}</h3>
                    <div class="product-brand">${product.brand}</div>
                    <p class="product-description">${product.description}</p>
                    <div class="product-meta">
                        <span>${product.occasion}</span>
                        <span>${product.season}</span>
                        <span>${product.style}</span>
                    </div>
                    <div class="product-details">
                        <div class="product-price">${product.price} RON</div>
                        <div class="product-rating">★ ${product.rating}</div>
                    </div>
                </div>
            `;
            
            return card;
        }
        
        /**
         * Configurează paginarea
         */
        function setupPagination(pagination) {
            if (pagination.pages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }
            
            let paginationHTML = '<div class="pagination">';
            
            // Buton Previous
            paginationHTML += `
                <button ${pagination.page <= 1 ? 'disabled' : ''} 
                        onclick="changePage(${pagination.page - 1})">
                    &laquo; Anterior
                </button>
            `;
            
            // Numerele paginilor
            const startPage = Math.max(1, pagination.page - 2);
            const endPage = Math.min(pagination.pages, pagination.page + 2);
            
            if (startPage > 1) {
                paginationHTML += '<button onclick="changePage(1)">1</button>';
                if (startPage > 2) {
                    paginationHTML += '<span>...</span>';
                }
            }
            
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === pagination.page ? 'active' : '';
                paginationHTML += `<button class="${activeClass}" onclick="changePage(${i})">${i}</button>`;
            }
            
            if (endPage < pagination.pages) {
                if (endPage < pagination.pages - 1) {
                    paginationHTML += '<span>...</span>';
                }
                paginationHTML += `<button onclick="changePage(${pagination.pages})">${pagination.pages}</button>`;
            }
            
            // Buton Next
            paginationHTML += `
                <button ${pagination.page >= pagination.pages ? 'disabled' : ''} 
                        onclick="changePage(${pagination.page + 1})">
                    Următorul &raquo;
                </button>
            `;
            
            paginationHTML += '</div>';
            paginationContainer.innerHTML = paginationHTML;
        }
        
        /**
         * Schimbă pagina
         */
        function changePage(page) {
            if (page < 1 || page > totalPages || page === currentPage || isLoading) {
                return;
            }
            
            const filters = getCurrentFilters();
            loadProducts(page, filters);
            
            // Scroll la top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        /**
         * Obține filtrele curente
         */
        function getCurrentFilters() {
            const filters = {};
            
            if (brandFilter.value) filters.brand = brandFilter.value;
            if (styleFilter.value) filters.style = styleFilter.value;
            if (occasionFilter.value) filters.occasion = occasionFilter.value;
            
            return filters;
        }
        
        /**
         * Aplică filtrele
         */
        function applyFilters() {
            const filters = getCurrentFilters();
            currentPage = 1; // Reset la prima pagină
            loadProducts(1, filters);
        }
        
        /**
         * Resetează filtrele
         */
        function clearFilters() {
            brandFilter.value = '';
            styleFilter.value = '';
            occasionFilter.value = '';
            applyFilters();
        }
        
        /**
         * Afișează eroare
         */
        function showError(message) {
            productsContainer.innerHTML = `<div class="error">${message}</div>`;
            paginationContainer.innerHTML = '';
        }
        
        // Event listeners
        applyFiltersBtn.addEventListener('click', applyFilters);
        clearFiltersBtn.addEventListener('click', clearFilters);
        
        // Aplică filtrele la schimbarea dropdown-urilor (opțional)
        [brandFilter, styleFilter, occasionFilter].forEach(filter => {
            filter.addEventListener('change', () => {
                // Auto-apply după 500ms
                clearTimeout(window.filterTimeout);
                window.filterTimeout = setTimeout(applyFilters, 500);
            });
        });
        
        // Încarcă produsele la încărcarea paginii
        document.addEventListener('DOMContentLoaded', () => {
            loadProducts();
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) return;
            
            switch(e.key) {
                case 'ArrowLeft':
                    if (currentPage > 1) changePage(currentPage - 1);
                    break;
                case 'ArrowRight':
                    if (currentPage < totalPages) changePage(currentPage + 1);
                    break;
            }
        });
    </script>
</body>
</html>