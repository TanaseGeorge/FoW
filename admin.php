<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrare - ShoeReco</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f8f9fa;
            color: #333;
        }
        
        .header {
            background: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-tabs {
            display: flex;
            background: #34495e;
            padding: 0 2rem;
        }
        
        .nav-tab {
            padding: 1rem 2rem;
            background: none;
            border: none;
            color: #ecf0f1;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .nav-tab:hover,
        .nav-tab.active {
            background: #3498db;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }
        
        .card-header {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #3498db;
        }
        
        .stat-label {
            color: #666;
            margin-top: 0.5rem;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .table tr:hover {
            background: #f8f9fa;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
            margin-right: 0.5rem;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-success {
            background: #27ae60;
            color: white;
        }
        
        .btn-success:hover {
            background: #219a52;
        }
        
        .loading {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        
        .error {
            color: #e74c3c;
            background: #fdf0ed;
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
        }
        
        .hidden {
            display: none;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
        }
        
        .close {
            float: right;
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
        }
        
        .close:hover {
            color: #333;
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 0 1rem;
            }
            
            .nav-tabs {
                flex-wrap: wrap;
                padding: 0;
            }
            
            .nav-tab {
                flex: 1;
                text-align: center;
                padding: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛠️ Panel Administrare ShoeReco</h1>
        <div>
            <span id="adminUser">Admin</span>
            <a href="index.php" class="btn btn-primary">Înapoi la site</a>
            <a href="logout.php" class="btn btn-danger">Deconectare</a>
        </div>
    </div>
    
    <div class="nav-tabs">
        <button class="nav-tab active" data-tab="dashboard">📊 Dashboard</button>
        <button class="nav-tab" data-tab="users">👥 Utilizatori</button>
        <button class="nav-tab" data-tab="products">👟 Produse</button>
        <button class="nav-tab" data-tab="statistics">📈 Statistici</button>
    </div>
    
    <div class="main-content">
        <!-- Dashboard Tab -->
        <div id="dashboard" class="tab-content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="totalUsers">-</div>
                    <div class="stat-label">Utilizatori</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="totalProducts">-</div>
                    <div class="stat-label">Produse</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="totalRecommendations">-</div>
                    <div class="stat-label">Recomandări</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="activeUsers">-</div>
                    <div class="stat-label">Utilizatori Activi</div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Statistici Rapide</h3>
                </div>
                <div class="card-body">
                    <div id="quickStats">Se încarcă...</div>
                </div>
            </div>
        </div>
        
        <!-- Users Tab -->
        <div id="users" class="tab-content hidden">
            <div class="card">
                <div class="card-header">
                    <h3>Gestionare Utilizatori</h3>
                    <button class="btn btn-primary" onclick="showAddUserModal()">➕ Adaugă Utilizator</button>
                </div>
                <div class="card-body">
                    <div id="usersContent">Se încarcă utilizatorii...</div>
                </div>
            </div>
        </div>
        
        <!-- Products Tab -->
        <div id="products" class="tab-content hidden">
            <div class="card">
                <div class="card-header">
                    <h3>Gestionare Produse</h3>
                    <button class="btn btn-primary" onclick="showAddProductModal()">➕ Adaugă Produs</button>
                </div>
                <div class="card-body">
                    <div id="productsContent">Se încarcă produsele...</div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Tab -->
        <div id="statistics" class="tab-content hidden">
            <div class="card">
                <div class="card-header">
                    <h3>Statistici Detaliate</h3>
                    <button class="btn btn-success" onclick="exportData('json')">📄 Export JSON</button>
                </div>
                <div class="card-body">
                    <div id="statisticsContent">Se încarcă statisticile...</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal pentru adăugare utilizator -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('userModal')">&times;</span>
            <h3>Adaugă Utilizator Nou</h3>
            <form id="addUserForm">
                <div class="form-group">
                    <label>Nume:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Parolă:</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Adaugă</button>
            </form>
        </div>
    </div>
    
    <!-- Modal pentru adăugare produs -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('productModal')">&times;</span>
            <h3>Adaugă Produs Nou</h3>
            <form id="addProductForm">
                <div class="form-group">
                    <label>Nume:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Brand:</label>
                    <input type="text" name="brand" required>
                </div>
                <div class="form-group">
                    <label>Preț (RON):</label>
                    <input type="number" step="0.01" name="price" required>
                </div>
                <div class="form-group">
                    <label>Ocazie:</label>
                    <select name="occasion" required>
                        <option value="sport">Sport</option>
                        <option value="casual">Casual</option>
                        <option value="elegant">Elegant</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Sezon:</label>
                    <select name="season" required>
                        <option value="primavara">Primăvară</option>
                        <option value="vara">Vară</option>
                        <option value="toamna">Toamnă</option>
                        <option value="iarna">Iarnă</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Stil:</label>
                    <select name="style" required>
                        <option value="sneakers">Sneakers</option>
                        <option value="cizme">Cizme</option>
                        <option value="sandale">Sandale</option>
                        <option value="papuci">Papuci</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Descriere:</label>
                    <input type="text" name="description">
                </div>
                <div class="form-group">
                    <label>URL Imagine:</label>
                    <input type="url" name="image_url">
                </div>
                <button type="submit" class="btn btn-primary">Adaugă</button>
            </form>
        </div>
    </div>

    <script>
        // Starea aplicației admin
        let currentTab = 'dashboard';
        
        // Inițializare la încărcarea paginii
        document.addEventListener('DOMContentLoaded', function() {
            setupNavigation();
            loadDashboard();
        });
        
        /**
         * Configurează navigația între tab-uri
         */
        function setupNavigation() {
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const targetTab = this.dataset.tab;
                    switchTab(targetTab);
                });
            });
        }
        
        /**
         * Schimbă tab-ul activ
         */
        function switchTab(tabName) {
            // Actualizează tab-urile
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
            
            // Ascunde toate conținuturile
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Afișează conținutul target
            document.getElementById(tabName).classList.remove('hidden');
            
            currentTab = tabName;
            
            // Încarcă datele pentru tab-ul curent
            loadTabData(tabName);
        }
        
        /**
         * Încarcă datele pentru tab-ul specificat
         */
        function loadTabData(tabName) {
            switch(tabName) {
                case 'dashboard':
                    loadDashboard();
                    break;
                case 'users':
                    loadUsers();
                    break;
                case 'products':
                    loadProducts();
                    break;
                case 'statistics':
                    loadStatistics();
                    break;
            }
        }
        
        /**
         * Încarcă dashboard-ul
         */
        async function loadDashboard() {
            try {
                // Încarcă statisticile pentru dashboard
                const [usersResponse, productsResponse, statsResponse] = await Promise.all([
                    fetch('api.php?action=admin&entity=users'),
                    fetch('api.php?action=admin&entity=products'),
                    fetch('api.php?action=admin&entity=stats')
                ]);
                
                const usersData = await usersResponse.json();
                const productsData = await productsResponse.json();
                const statsData = await statsResponse.json();
                
                // Actualizează cardurile de statistici
                if (usersData.success) {
                    document.getElementById('totalUsers').textContent = usersData.data.users.length;
                }
                
                if (productsData.success) {
                    document.getElementById('totalProducts').textContent = productsData.data.products.length;
                }
                
                if (statsData.success && statsData.data.admin_stats.length > 0) {
                    const totalRecs = statsData.data.admin_stats.reduce((sum, stat) => sum + parseInt(stat.count), 0);
                    const activeUsers = statsData.data.admin_stats[0].active_users || 0;
                    
                    document.getElementById('totalRecommendations').textContent = totalRecs;
                    document.getElementById('activeUsers').textContent = activeUsers;
                    
                    // Afișează statistici rapide
                    displayQuickStats(statsData.data.admin_stats);
                }
                
            } catch (error) {
                console.error('Eroare la încărcarea dashboard-ului:', error);
            }
        }
        
        /**
         * Afișează statisticile rapide
         */
        function displayQuickStats(stats) {
            let html = '<h4>Popularitatea ocaziilor:</h4><ul>';
            stats.forEach(stat => {
                html += `<li><strong>${stat.occasion}:</strong> ${stat.count} recomandări</li>`;
            });
            html += '</ul>';
            
            document.getElementById('quickStats').innerHTML = html;
        }
        
        /**
         * Încarcă utilizatorii
         */
        async function loadUsers() {
            try {
                const response = await fetch('api.php?action=admin&entity=users');
                const result = await response.json();
                
                if (result.success) {
                    displayUsers(result.data.users);
                } else {
                    document.getElementById('usersContent').innerHTML = '<div class="error">Eroare la încărcarea utilizatorilor.</div>';
                }
            } catch (error) {
                console.error('Eroare la încărcarea utilizatorilor:', error);
                document.getElementById('usersContent').innerHTML = '<div class="error">Eroare de conexiune.</div>';
            }
        }
        
        /**
         * Afișează tabelul cu utilizatori
         */
        function displayUsers(users) {
            let html = `
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nume</th>
                            <th>Email</th>
                            <th>Data Înregistrării</th>
                            <th>Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            users.forEach(user => {
                html += `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.created_at || 'N/A'}</td>
                        <td>
                            <button class="btn btn-danger" onclick="deleteUser(${user.id})">🗑️ Șterge</button>
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            document.getElementById('usersContent').innerHTML = html;
        }
        
        /**
         * Încarcă produsele
         */
        async function loadProducts() {
            try {
                const response = await fetch('api.php?action=admin&entity=products');
                const result = await response.json();
                
                if (result.success) {
                    displayProducts(result.data.products);
                } else {
                    document.getElementById('productsContent').innerHTML = '<div class="error">Eroare la încărcarea produselor.</div>';
                }
            } catch (error) {
                console.error('Eroare la încărcarea produselor:', error);
                document.getElementById('productsContent').innerHTML = '<div class="error">Eroare de conexiune.</div>';
            }
        }
        
        /**
         * Afișează tabelul cu produse
         */
        function displayProducts(products) {
            let html = `
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nume</th>
                            <th>Brand</th>
                            <th>Preț</th>
                            <th>Rating</th>
                            <th>Stil</th>
                            <th>Acțiuni</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            products.forEach(product => {
                html += `
                    <tr>
                        <td>${product.id}</td>
                        <td>${product.name}</td>
                        <td>${product.brand}</td>
                        <td>${product.price} RON</td>
                        <td>${product.rating}/5</td>
                        <td>${product.style}</td>
                        <td>
                            <button class="btn btn-danger" onclick="deleteProduct(${product.id})">🗑️ Șterge</button>
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            document.getElementById('productsContent').innerHTML = html;
        }
        
        /**
         * Încarcă statisticile
         */
        async function loadStatistics() {
            try {
                const response = await fetch('api.php?action=admin&entity=stats');
                const result = await response.json();
                
                if (result.success) {
                    displayDetailedStats(result.data.admin_stats);
                } else {
                    document.getElementById('statisticsContent').innerHTML = '<div class="error">Eroare la încărcarea statisticilor.</div>';
                }
            } catch (error) {
                console.error('Eroare la încărcarea statisticilor:', error);
                document.getElementById('statisticsContent').innerHTML = '<div class="error">Eroare de conexiune.</div>';
            }
        }
        
        /**
         * Afișează statisticile detaliate
         */
        function displayDetailedStats(stats) {
            let html = `
                <h4>Distribuția recomandărilor pe ocazii:</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ocazie</th>
                            <th>Numărul de Recomandări</th>
                            <th>Utilizatori Activi</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            stats.forEach(stat => {
                html += `
                    <tr>
                        <td>${stat.occasion}</td>
                        <td>${stat.count}</td>
                        <td>${stat.active_users}</td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            document.getElementById('statisticsContent').innerHTML = html;
        }
        
        /**
         * Exportă datele în format JSON
         */
        function exportData(format) {
            window.open(`api.php?action=export&format=${format}`, '_blank');
        }
        
        /**
         * Afișează modal-ul pentru adăugare utilizator
         */
        function showAddUserModal() {
            document.getElementById('userModal').style.display = 'block';
        }
        
        /**
         * Afișează modal-ul pentru adăugare produs
         */
        function showAddProductModal() {
            document.getElementById('productModal').style.display = 'block';
        }
        
        /**
         * Închide modal-ul specificat
         */
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        /**
         * Șterge un utilizator
         */
        function deleteUser(userId) {
            if (confirm('Ești sigur că vrei să ștergi acest utilizator?')) {
                alert('Funcționalitatea de ștergere va fi implementată.');
                // TODO: Implementează DELETE request
            }
        }
        
        /**
         * Șterge un produs
         */
        function deleteProduct(productId) {
            if (confirm('Ești sigur că vrei să ștergi acest produs?')) {
                alert('Funcționalitatea de ștergere va fi implementată.');
                // TODO: Implementează DELETE request
            }
        }
        
        // Închide modal-urile când se dă click în afara lor
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
        
        // TODO: Implementează formularele pentru adăugare
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Formularul de adăugare utilizator va fi implementat.');
        });
        
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Formularul de adăugare produs va fi implementat.');
        });
    </script>
</body>
</html>