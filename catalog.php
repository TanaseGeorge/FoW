<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartFoot - Catalog</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="Styling/catalogstyle.css">
   
</head>
<body>
    <header>
        <h1>SmartFoot</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="catalog.php" class="active">Catalog</a>
            <a href="recommendations.php">Recomandări</a>
            <a href="statistics.php">Statistici</a>
            <a href="logout.php">Deconectare</a>
        </nav>
    </header>

    <main>        
        <!-- Filtre -->
        <div class="filters">
            <!-- Căutare după cuvinte cheie -->
            <input type="text" id="searchFilter" placeholder="Căutare..." />
            
            <!-- Filtru ocazie -->
            <select id="occasionFilter">
                <option value="">-- Ocazie --</option>
                <option value="casual">Casual</option>
                <option value="sport">Sport</option>
                <option value="elegant">Elegant</option>
            </select>
            
            <!-- Filtru sezon -->
            <select id="seasonFilter">
                <option value="">-- Sezon --</option>
                <option value="vara">Vara</option>
                <option value="iarna">Iarna</option>
                <option value="toamna">Toamna</option>
                <option value="primavara">Primăvara</option>
            </select>
            
            <!-- Filtru stil -->
            <select id="styleFilter">
                <option value="">-- Stil --</option>
                <option value="sneakers">Sneakers</option>
                <option value="sandale">Sandale</option>
                <option value="papuci">Papuci</option>
                <option value="cizme">Cizme</option>
            </select>
            
            <!-- Preț minim și maxim (ca în versiunea veche) -->
            <input type="number" id="priceMinFilter" placeholder="Preț minim" />
            <input type="number" id="priceMaxFilter" placeholder="Preț maxim" />
            
            <button id="applyFilters">Filtrează</button>
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
        let currentPage = 1;
        let totalPages = 1;
        let isLoading = false;

        // Referințe la elemente CORECTE (fără brandFilter)
        const productsContainer = document.getElementById('productsContainer');
        const paginationContainer = document.getElementById('paginationContainer');
        const searchFilter = document.getElementById('searchFilter');
        const occasionFilter = document.getElementById('occasionFilter');
        const seasonFilter = document.getElementById('seasonFilter');
        const styleFilter = document.getElementById('styleFilter');
        const priceMinFilter = document.getElementById('priceMinFilter');
        const priceMaxFilter = document.getElementById('priceMaxFilter');
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
         * Obține filtrele curente - CORECTAT
         */
        function getCurrentFilters() {
            const filters = {};
            
            if (searchFilter.value.trim()) filters.search = searchFilter.value.trim();
            if (occasionFilter.value) filters.occasion = occasionFilter.value;
            if (seasonFilter.value) filters.season = seasonFilter.value;
            if (styleFilter.value) filters.style = styleFilter.value;
            
            // Prețuri separate (ca în versiunea veche)
            if (priceMinFilter.value) filters.price_min = priceMinFilter.value;
            if (priceMaxFilter.value) filters.price_max = priceMaxFilter.value;
            
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
         * Resetează filtrele - CORECTAT
         */
        function clearFilters() {
            searchFilter.value = '';
            occasionFilter.value = '';
            seasonFilter.value = '';
            styleFilter.value = '';
            priceMinFilter.value = '';
            priceMaxFilter.value = '';
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

        // Auto-aplicare pentru toate filtrele - CORECTAT
        [searchFilter, occasionFilter, seasonFilter, styleFilter, priceMinFilter, priceMaxFilter].forEach(filter => {
            filter.addEventListener('change', () => {
                clearTimeout(window.filterTimeout);
                window.filterTimeout = setTimeout(applyFilters, 500);
            });
            
            // Pentru search și preț, aplică la fiecare tastă (cu debounce)
            if (filter.type === 'text' || filter.type === 'number') {
                filter.addEventListener('input', () => {
                    clearTimeout(window.filterTimeout);
                    window.filterTimeout = setTimeout(applyFilters, 800);
                });
            }
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