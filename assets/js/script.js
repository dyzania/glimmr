// Example implementation
document.querySelector('.feeling-picker-btn').addEventListener('click', function() {
    // Show a modal with feeling options
    // Would need additional HTML/CSS/JS
});

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
    
    // Media preview
    const mediaInput = document.getElementById('media');
    if (mediaInput) {
        mediaInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const previewContainer = document.createElement('div');
                previewContainer.className = 'media-preview mb-3';
                
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'img-fluid';
                    img.style.maxHeight = '300px';
                    previewContainer.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = URL.createObjectURL(file);
                    video.controls = true;
                    video.className = 'w-100';
                    previewContainer.appendChild(video);
                }
                
                const existingPreview = document.querySelector('.media-preview');
                if (existingPreview) {
                    existingPreview.replaceWith(previewContainer);
                } else {
                    mediaInput.parentNode.insertBefore(previewContainer, mediaInput.nextSibling);
                }
            }
        });
    }
});