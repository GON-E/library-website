


//console.log("ang pogi k0"); 

const showPassword = document.querySelector("#show-password");
const passwordField = document.querySelector("#password");

showPassword.addEventListener("click", () => {
    showPassword.classList.toggle("fa-eye-slash");
    passwordField.type = passwordField.type === "password" ? "text" : "password";
});


// Get HTML
// EDIT LOGIN ATTEMPT
/*
function countAttempt() {
  document.querySelector('login-button').addEventListener('click',() => {
  })
}
  */


    let attempt = document.querySelector('.login-attempt').innerHTML;

    console.log(attempt);