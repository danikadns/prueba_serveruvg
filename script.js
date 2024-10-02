
const btnSignIn = document.getElementById("sign-in"),
      btnSignUp = document.getElementById("sign-up"),
      containerFormRegister = document.querySelector(".register"),
      containerFormLogin = document.querySelector(".login");

btnSignIn.addEventListener("click", e => {
    containerFormRegister.classList.add("hide");
    containerFormLogin.classList.remove("hide")
})


btnSignUp.addEventListener("click", e => {
    containerFormLogin.classList.add("hide");
    containerFormRegister.classList.remove("hide")
})
/*
document.getElementById('sign-in').addEventListener('click', function() {
    document.querySelector('.login').classList.remove('hide');
    document.querySelector('.register').classList.add('hide');
});

document.getElementById('sign-up').addEventListener('click', function() {
    document.querySelector('.register').classList.remove('hide');
    document.querySelector('.login').classList.add('hide');
});
*/