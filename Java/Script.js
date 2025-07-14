document.getElementById("btn__iniciar-sesion").addEventListener("click", iniciarSesion);
document.getElementById("btn__registrarse").addEventListener("click", register);
window.addEventListener("resize", anchoPagina);

// Declaración de variables
var contenedorLoginRegister = document.querySelector(".Contenedor__login-register");
var formularioLogin = document.querySelector(".login-form");
var formularioRegister = document.querySelector(".formulario__register");
var cajaTraseraLogin = document.querySelector(".login-container");
var cajaTraseraRegister = document.querySelector(".register-form");

function anchoPagina(){
    if(window.innerWidth > 850){
        cajaTraseraLogin.style.display = "block";
        cajaTraseraRegister.style.display = "block";
    }else{
        cajaTraseraRegister.style.display = "block";
        cajaTraseraRegister.style.opacity = "1";
        cajaTraseraLogin.style.display = "none";
        formularioLogin.style.display = "block";
        formularioRegister.style.display = "none";
        contenedorLoginRegister.style.left = "0px";
    }
}

anchoPagina()

function iniciarSesion() {
    if (contenedorLoginRegister && formularioLogin && formularioRegister && cajaTraseraLogin && cajaTraseraRegister) {
        if (window.innerWidth > 850) {
            formularioRegister.style.display = "none";
            contenedorLoginRegister.style.left = "10px";
            formularioLogin.style.display = "block";
            cajaTraseraRegister.style.opacity = "1";
            cajaTraseraLogin.style.opacity = "0";
        } else {
            formularioRegister.style.display = "none";
            contenedorLoginRegister.style.left = "0px";
            formularioLogin.style.display = "block";
            cajaTraseraRegister.style.display = "block";
            cajaTraseraLogin.style.display = "none";
        }
    }
}

function register() {
    if (contenedorLoginRegister && formularioLogin && formularioRegister && cajaTraseraLogin && cajaTraseraRegister) {
        if (window.innerWidth > 850) {
            formularioRegister.style.display = "block";
            contenedorLoginRegister.style.left = "410px";
            formularioLogin.style.display = "none";
            cajaTraseraRegister.style.opacity = "0";
            cajaTraseraLogin.style.opacity = "1";
        } else {
            formularioRegister.style.display = "block";
            contenedorLoginRegister.style.left = "0px";
            formularioLogin.style.display = "none";
            cajaTraseraRegister.style.display = "none";
            cajaTraseraLogin.style.display = "block";
            cajaTraseraLogin.style.opacity = "1";
        }
    }
}

