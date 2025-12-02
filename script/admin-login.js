const showPassword = document.querySelector("#show-password");
const passwordField = document.querySelector("#password");

if (showPassword && passwordField) {
    showPassword.addEventListener("click", () => {
        if (showPassword.classList.contains("fa-eye-slash")) {
            showPassword.classList.remove("fa-eye-slash");
            showPassword.classList.add("fa-eye");
        } else {
            showPassword.classList.remove("fa-eye");
            showPassword.classList.add("fa-eye-slash");
        }
        
        if (passwordField.type === "password") {
            passwordField.type = "text";
        } else {
            passwordField.type = "password";
        }
    });
}

const MAX_ATTEMPTS = 5;
const LOCKOUT_TIME = 15 * 60 * 1000;

const loginForm = document.querySelector("form");
const loginButton = document.querySelector(".login-button");
const attemptDisplay = document.querySelector(".login-attempt");

function getAttempts() {
    const stored = localStorage.getItem("loginAttempts");
    return stored ? parseInt(stored) : 0;
}

function getLockoutTime() {
    return localStorage.getItem("lockoutTime");
}

function setAttempts(count) {
    localStorage.setItem("loginAttempts", count);
    updateAttemptDisplay(count);
}

function setLockoutTime() {
    localStorage.setItem("lockoutTime", Date.now().toString());
}

function clearLockout() {
    localStorage.removeItem("loginAttempts");
    localStorage.removeItem("lockoutTime");
    updateAttemptDisplay(0);
}

function updateAttemptDisplay(attempts) {
    if (attemptDisplay) {
        attemptDisplay.innerHTML = `Login Attempts: ${attempts}`;
        
        if (attempts >= MAX_ATTEMPTS) {
            attemptDisplay.style.color = "#dc3545";
            attemptDisplay.innerHTML = `🔒 Account Locked`;
        } else if (attempts >= 3) {
            attemptDisplay.style.color = "#ffc107";
        } else {
            attemptDisplay.style.color = "#6c757d";
        }
    }
}

function isAccountLocked() {
    const attempts = getAttempts();
    const lockoutTime = getLockoutTime();
    
    if (attempts >= MAX_ATTEMPTS && lockoutTime) {
        const timeElapsed = Date.now() - parseInt(lockoutTime);
        
        if (timeElapsed < LOCKOUT_TIME) {
            return {
                locked: true,
                remainingTime: Math.ceil((LOCKOUT_TIME - timeElapsed) / 1000 / 60)
            };
        } else {
            clearLockout();
            return { locked: false };
        }
    }
    
    return { locked: false };
}

function showLockoutMessage(remainingTime) {
    if (attemptDisplay) {
        attemptDisplay.innerHTML = `🔒 Too many attempts. Try again in ${remainingTime} minutes.`;
        attemptDisplay.style.color = "#dc3545";
    }
    
    if (loginButton) {
        loginButton.disabled = true;
        loginButton.style.opacity = "0.5";
        loginButton.style.cursor = "not-allowed";
    }
    
    const inputs = loginForm.querySelectorAll("input");
    inputs.forEach(input => input.disabled = true);
}

function incrementAttempts() {
    let attempts = getAttempts() + 1;
    setAttempts(attempts);
    
    if (attempts >= MAX_ATTEMPTS) {
        setLockoutTime();
        showLockoutMessage(15);
    }
    
    return attempts;
}

document.addEventListener("DOMContentLoaded", () => {
    const currentAttempts = getAttempts();
    updateAttemptDisplay(currentAttempts);
    
    const lockStatus = isAccountLocked();
    if (lockStatus.locked) {
        showLockoutMessage(lockStatus.remainingTime);
    }
    
    if (loginForm) {
        loginForm.addEventListener("submit", (e) => {
            const lockStatus = isAccountLocked();
            if (lockStatus.locked) {
                e.preventDefault();
                showLockoutMessage(lockStatus.remainingTime);
                return false;
            }
        });
    }
});

window.addEventListener("load", () => {
    const errorMessage = document.querySelector(".error-message, .alert-danger");
    
    if (errorMessage && errorMessage.textContent.trim() !== "") {
        incrementAttempts();
    }
    
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("success") === "true") {
        clearLockout();
    }
});

function startLockoutCountdown() {
    const lockStatus = isAccountLocked();
    
    if (lockStatus.locked && attemptDisplay) {
        const countdownInterval = setInterval(() => {
            const currentLockStatus = isAccountLocked();
            
            if (!currentLockStatus.locked) {
                clearInterval(countdownInterval);
                location.reload();
            } else {
                attemptDisplay.innerHTML = `🔒 Try again in ${currentLockStatus.remainingTime} minutes`;
            }
        }, 60000);
    }
}

startLockoutCountdown();

function resetAttempts() {
    clearLockout();
    location.reload();
}

window.resetAttempts = resetAttempts;