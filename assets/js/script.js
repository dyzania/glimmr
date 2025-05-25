
document.addEventListener('DOMContentLoaded', function() {
    // Password match validation
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    if (password && confirmPassword) {
        function validatePassword() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity("Passwords don't match");
                document.getElementById('password-feedback').textContent = "Passwords don't match";
                confirmPassword.classList.add('is-invalid');
            } else {
                confirmPassword.setCustomValidity('');
                document.getElementById('password-feedback').textContent = '';
                confirmPassword.classList.remove('is-invalid');
            }
        }
        
        password.onchange = validatePassword;
        confirmPassword.onkeyup = validatePassword;
    }
    
    // Username availability check
    const usernameInput = document.getElementById('username');
    if (usernameInput) {
        usernameInput.addEventListener('blur', function() {
            const username = this.value;
            if (username.length > 0) {
                fetch(`/api/check_username.php?username=${username}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.available) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                            document.getElementById('username-feedback').textContent = '';
                        } else {
                            this.classList.remove('is-valid');
                            this.classList.add('is-invalid');
                            document.getElementById('username-feedback').textContent = 'Username is already taken';
                        }
                    });
            }
        });
    }

    
    // Email availability check
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value;
            if (email.length > 0) {
                fetch(`/api/check_email.php?email=${email}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.available) {
                            this.classList.remove('is-invalid');
                            this.classList.add('is-valid');
                            document.getElementById('email-feedback').textContent = '';
                        } else {
                            this.classList.remove('is-valid');
                            this.classList.add('is-invalid');
                            document.getElementById('email-feedback').textContent = 'Email is already registered';
                        }
                    });
            }
        });
    }   
});

function validatePassword() {
    const passwordInput = document.getElementById('password');
    const errorElement = document.getElementById('password-error');
    
    if (passwordInput.value.length > 0 && passwordInput.value.length < 8) {
        errorElement.style.display = 'block';
        passwordInput.classList.add('is-invalid');
    } else {
        errorElement.style.display = 'none';
        passwordInput.classList.remove('is-invalid');
    }
}

// Also validate on form submission
document.querySelector('form').addEventListener('submit', function(e) {
    const passwordInput = document.getElementById('password');
    if (passwordInput.value.length < 8) {
        e.preventDefault();
        document.getElementById('password-error').style.display = 'block';
        passwordInput.classList.add('is-invalid');
        passwordInput.focus();
    }
});