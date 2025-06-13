<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare - ShoeReco</title>
    <link rel="stylesheet" href="auth.css">
    <style>
        .message {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 4px;
            text-align: center;
            display: none;
        }
        
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .auth-container {
        max-width: 400px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .auth-container form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        }
        
        .auth-container input {
        padding: 0.8rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        }
        
        .auth-container button {
        padding: 1rem;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.3s ease;
        }
        
        .auth-container button:hover {
        background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>Autentificare</h2>
        
        <div id="message" class="message"></div>
        
        <form id="loginForm">
            <input type="email" name="email" id="email" placeholder="Email" required>
            <input type="password" name="password" id="password" placeholder="Parolă" required>
            <button type="submit" id="submitBtn">Autentificare</button>
        </form>
        
        <p>Nu ai cont? <a href="register.php">Înregistrează-te</a></p>
    </div>

    <script>
        // Referințe la elemente
        const loginForm = document.getElementById('loginForm');
        const messageDiv = document.getElementById('message');
        const submitBtn = document.getElementById('submitBtn');
        
        /**
         * Afișează mesaj către utilizator
         */
        function showMessage(text, type = 'error') {
            messageDiv.textContent = text;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = 'block';
            
            // Ascunde mesajul după 5 secunde pentru success
            if (type === 'success') {
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 5000);
            }
        }
        
        /**
         * Setează starea de loading
         */
        function setLoading(loading) {
            if (loading) {
                submitBtn.textContent = 'Se autentifică...';
                loginForm.classList.add('loading');
            } else {
                submitBtn.textContent = 'Autentificare';
                loginForm.classList.remove('loading');
            }
        }
        
        /**
         * Gestionează submit-ul formularului
         */
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Ascunde mesajele anterioare
            messageDiv.style.display = 'none';
            
            // Obține datele din formular
            const formData = new FormData(loginForm);
            const loginData = {
                email: formData.get('email'),
                password: formData.get('password')
            };
            
            // Validare de bază
            if (!loginData.email || !loginData.password) {
                showMessage('Toate câmpurile sunt obligatorii', 'error');
                return;
            }
            
            setLoading(true);
            
            try {
                // Trimite cererea AJAX
                const response = await fetch('api.php?action=login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(loginData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage('Autentificare reușită! Redirecționare...', 'success');
                    
                    // Salvează informațiile utilizatorului (opțional)
                    if (result.data && result.data.user) {
                        sessionStorage.setItem('user', JSON.stringify(result.data.user));
                    }
                    
                    // Redirecționează după 1.5 secunde
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                    
                } else {
                    showMessage(result.message || 'Eroare la autentificare', 'error');
                }
                
            } catch (error) {
                console.error('Eroare de rețea:', error);
                showMessage('Eroare de conexiune. Verificați internetul și încercați din nou.', 'error');
            } finally {
                setLoading(false);
            }
        });
        
        /**
         * Verifică dacă utilizatorul este deja autentificat
         */
        function checkAuthStatus() {
            // Poți adăuga aici logică pentru a verifica dacă utilizatorul e deja logat
            const userData = sessionStorage.getItem('user');
            if (userData) {
                // Opțional: verifică cu server-ul dacă sesiunea e validă
                console.log('User data found:', JSON.parse(userData));
            }
        }
        
        // Verifică statusul la încărcarea paginii
        document.addEventListener('DOMContentLoaded', checkAuthStatus);
        
        /**
         * Gestionează Enter key pentru submit rapid
         */
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !loginForm.classList.contains('loading')) {
                loginForm.dispatchEvent(new Event('submit'));
            }
        });
        
        /**
         * Focus automat pe primul câmp
         */
        document.getElementById('email').focus();
    </script>
</body>
</html>