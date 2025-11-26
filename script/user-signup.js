document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');
    const submitBtn = document.getElementById('submitBtn');


    function togglePasswordVisibility(input, button) {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        
        const icon = button.querySelector('i');
        if (type === 'text') {
            icon.setAttribute('data-feather', 'eye-off');
            input.classList.add('password-revealed');
        } else {
            icon.setAttribute('data-feather', 'eye');
            input.classList.remove('password-revealed');
        }
        feather.replace();
    }

    togglePasswordBtn.addEventListener('click', function() {
        togglePasswordVisibility(passwordInput, this);
    });

    toggleConfirmPasswordBtn.addEventListener('click', function() {
        togglePasswordVisibility(confirmPasswordInput, this);
    });

    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (passwordInput.value !== confirmPasswordInput.value) {
            alert("Passwords don't match!");
            return;
        }
        
        if (passwordInput.value.length < 8) {
            alert("Password must be at least 8 characters long!");
            return;
        }
        
        alert("Password submitted successfully!");
    });

    feather.replace();
});

function checkUsernameAvailability(username) {
    const statusElement = document.getElementById('usernameStatus');
    
    if (username.length < 3) {
        statusElement.textContent = "Username must be at least 3 characters";
        statusElement.className = "text-red-500";
        return;
    }
    
    setTimeout(() => {

        const takenUsernames = ['admin', 'user', 'test', 'demo'];
        
        if (takenUsernames.includes(username.toLowerCase())) {
            statusElement.textContent = "Username is already taken";
            statusElement.className = "text-red-500";
        } else {
            statusElement.textContent = "Username is available";
            statusElement.className = "text-green-500";
        }
    }, 500);
}

document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        username: document.getElementById('username').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        authMethod: document.querySelector('input[name="authMethod"]:checked').value,
        authInput: document.getElementById('authInput').value
    };
    
    if (formData.password !== document.getElementById('confirmPassword').value) {
        alert("Passwords don't match!");
        return;
    }
    
    if (!formData.authInput) {
        alert("Please fill in your authentication information");
        return;
    }
    
    console.log("Form data to be submitted:", formData);
    
    alert(`Account created successfully! You can now log in. 
Authentication method: ${formData.authMethod}`);
});

document.addEventListener('input', function(e) {
    if (e.target.type === 'tel' && e.target.pattern === '[0-9]*') {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    }
});

document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthIndicator = document.querySelector('.password-strength-fill');
    let strength = 0;
    
    if (password.length > 0) strength += 1;
    if (password.length >= 8) strength += 1;
    if (/[A-Z]/.test(password)) strength += 1;
    if (/[0-9]/.test(password)) strength += 1;
    if (/[^A-Za-z0-9]/.test(password)) strength += 1;
    
    const width = strength * 20;
    let color = '#ef4444'; 
    
    if (strength > 2) color = '#f59e0b'; 
    if (strength > 3) color = '#10b981'; 
    
    strengthIndicator.style.width = `${width}%`;
    strengthIndicator.style.backgroundColor = color;
});

