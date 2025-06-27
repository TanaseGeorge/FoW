<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare - ShoeReco</title>
    <link rel="stylesheet" href="Styling/auth.css">
    <link rel="stylesheet" href="Styling/register.css">

</head>

<body>
    <div class="auth-container">
        <h2>Înregistrare</h2>
        
        <div id="message" class="message"></div>
        
        <form id="registerForm">
            <input type="text" name="name" id="name" placeholder="Nume complet" required>
            <input type="email" name="email" id="email" placeholder="Email" required>
            <input type="password" name="password" id="password" placeholder="Parolă" required>
            <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirmă parola" required>
            
            <div class="password-requirements" id="passwordRequirements" style="display: none;">
                <div class="requirement" id="lengthReq">• Minim 6 caractere</div>
                <div class="requirement" id="matchReq">• Parolele trebuie să fie identice</div>
            </div>
            
            <button type="submit" id="submitBtn">Înregistrează-te</button>
        </form>
        
        <p>Ai deja cont? <a href="login_form.php">Autentifică-te</a></p>
    </div>

    <script>
        // Referințe la elemente
        const registerForm = document.getElementById('registerForm');
        const messageDiv = document.getElementById('message');
        const submitBtn = document.getElementById('submitBtn');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const passwordRequirements = document.getElementById('passwordRequirements');
        const lengthReq = document.getElementById('lengthReq');
        const matchReq = document.getElementById('matchReq');
        
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
                submitBtn.textContent = 'Se înregistrează...';
                registerForm.classList.add('loading');
            } else {
                submitBtn.textContent = 'Înregistrează-te';
                registerForm.classList.remove('loading');
            }
        }
        
        /**
         * Validează parola în timp real
         */
        function validatePassword() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            // Verifică lungimea
            if (password.length >= 6) {
                lengthReq.classList.add('valid');
                lengthReq.classList.remove('invalid');
            } else {
                lengthReq.classList.add('invalid');
                lengthReq.classList.remove('valid');
            }
            
            // Verifică dacă parolele se potrivesc
            if (confirmPassword && password === confirmPassword) {
                matchReq.classList.add('valid');
                matchReq.classList.remove('invalid');
            } else if (confirmPassword) {
                matchReq.classList.add('invalid');
                matchReq.classList.remove('valid');
            } else {
                matchReq.classList.remove('valid', 'invalid');
            }
        }
        
        /**
         * Validare completă a formularului
         */
        function validateForm(formData) {
            const name = formData.get('name').trim();
            const email = formData.get('email').trim();
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            
            // Verificări de bază
            if (!name || !email || !password || !confirmPassword) {
                throw new Error('Toate câmpurile sunt obligatorii');
            }
            
            // Validare nume (minim 2 caractere)
            if (name.length < 2) {
                throw new Error('Numele trebuie să aibă minim 2 caractere');
            }
            
            // Validare email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                throw new Error('Adresa de email nu este validă');
            }
            
            // Validare parolă
            if (password.length < 6) {
                throw new Error('Parola trebuie să aibă minim 6 caractere');
            }
            
            // Verifică dacă parolele se potrivesc
            if (password !== confirmPassword) {
                throw new Error('Parolele nu se potrivesc');
            }
            
            return {
                name: name,
                email: email,
                password: password,
                confirm_password: confirmPassword
            };
        }
        
        /**
         * Gestionează submit-ul formularului
         */
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Ascunde mesajele anterioare
            messageDiv.style.display = 'none';
            
            try {
                // Obține și validează datele din formular
                const formData = new FormData(registerForm);
                const registrationData = validateForm(formData);
                
                setLoading(true);
                
                // Trimite cererea AJAX
                const response = await fetch('api.php?action=register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(registrationData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage('Cont creat cu succes! Redirecționare către login...', 'success');
                    
                    // Resetează formularul
                    registerForm.reset();
                    passwordRequirements.style.display = 'none';
                    
                    // Redirecționează după 2 secunde
                    setTimeout(() => {
                        window.location.href = 'login_form.php';
                    }, 2000);
                    
                } else {
                    showMessage(result.message || 'Eroare la înregistrare', 'error');
                }
                
            } catch (error) {
                if (error.message) {
                    // Eroare de validare
                    showMessage(error.message, 'error');
                } else {
                    // Eroare de rețea
                    console.error('Eroare de rețea:', error);
                    showMessage('Eroare de conexiune. Verificați internetul și încercați din nou.', 'error');
                }
            } finally {
                setLoading(false);
            }
        });
        
        /**
         * Event listeners pentru validare în timp real
         */
        passwordInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                passwordRequirements.style.display = 'block';
            } else {
                passwordRequirements.style.display = 'none';
            }
            validatePassword();
        });
        
        confirmPasswordInput.addEventListener('input', validatePassword);
        
        /**
         * Validare email în timp real
         */
        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            if (email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    this.style.borderColor = '#dc3545';
                } else {
                    this.style.borderColor = '#28a745';
                }
            } else {
                this.style.borderColor = '';
            }
        });
        
        /**
         * Validare nume în timp real
         */
        nameInput.addEventListener('blur', function() {
            const name = this.value.trim();
            if (name && name.length < 2) {
                this.style.borderColor = '#dc3545';
            } else if (name) {
                this.style.borderColor = '#28a745';
            } else {
                this.style.borderColor = '';
            }
        });
        
        /**
         * Gestionează Enter key pentru submit rapid
         */
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !registerForm.classList.contains('loading')) {
                registerForm.dispatchEvent(new Event('submit'));
            }
        });
        
        /**
         * Focus automat pe primul câmp
         */
        nameInput.focus();
    </script>
</body>
</html>