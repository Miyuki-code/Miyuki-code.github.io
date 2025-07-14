// Define primero la función agregarToast
const agregarToast = ({ tipo, titulo, descripcion, autoCierre }) => {
    // Crear nuevo toast
    console.log('Agregando toast:', tipo, titulo, descripcion, autoCierre);
    const nuevoToast = document.createElement('div');
    nuevoToast.style.display = 'flex';

    // Agregar clases correspondientes
    nuevoToast.classList.add('toast');
    nuevoToast.classList.add(tipo);
    if (autoCierre) {
        nuevoToast.classList.add('autoCierre');
    } else {
        nuevoToast.classList.add('noAutoCierre');
    }

    // Agregar id del toast
    const numeroAlAzar = Math.floor(Math.random() * 100);
    const fecha = Date.now();
    const toastId = `toast-${fecha}-${numeroAlAzar}`;
    nuevoToast.id = toastId;

    // Iconos
    const iconos = {
        exito: `<i class="fa-solid fa-square-check i"></i>`,
        error: `<i class="fa-solid fa-circle-exclamation i"></i>`,
        error2: `<i class="fa-solid fa-circle-exclamation i"></i>`,
        info: `<i class="fa-solid fa-circle-info i"></i>`,
        alert: `<i class="fa-solid fa-triangle-exclamation i"></i>`,
    };

    // Plantilla del toast
    const toast = `
        <div class="contenido">
            <div class="icono">
                ${iconos[tipo]}
            </div>
            <div class="texto">
                <p class="titulo">${titulo}</p>
                <p class="descripcion">${descripcion}</p>
            </div>
        </div>
        <button class="btn-cerrar">
            <div class="icono">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </button>
    `;

    // Agregar plantilla al nuevo toast
    nuevoToast.innerHTML = toast;

    // Agregar el nuevo toast al contenedor
    const contenedorToast = document.getElementById('contenedor-toast');
    if (contenedorToast) {
        contenedorToast.appendChild(nuevoToast);
    }

    // Función para manejar el cierre del toast
    const handleAnimacionCierre = (e) => {
        if (e.animationName === 'cierre') {
            nuevoToast.removeEventListener('animationend', handleAnimacionCierre);
            nuevoToast.remove();
        }
    };

    nuevoToast.addEventListener('animationend', handleAnimacionCierre);

    // Cierre automático
    if (autoCierre) {
        setTimeout(() => {
            nuevoToast.classList.add('cerrando');
        }, 5000);
    }
};

// Luego, agrega los event listeners
document.addEventListener('DOMContentLoaded', () => {
    const contenedorBotones = document.getElementById('contenedor-botones');
    const contenedorToast = document.getElementById('contenedor-toast');

    if (contenedorBotones) {
        contenedorBotones.addEventListener('click', (e) => {
            e.preventDefault();
            const tipo = e.target.dataset.tipo;

            if (tipo === 'registro', tipo === 'login') {
                agregarToast({ tipo: 'exito', titulo: 'Exito', descripcion: 'Usuario registrado con exito', autoCierre: true });
            } else if (tipo === 'error') {
                agregarToast({ tipo: 'error', titulo: 'Error', descripcion: 'Hubo un error al procesar la información.', autoCierre: true });
            } else if (tipo === 'error2') {
                agregarToast({ tipo: 'error2', titulo: 'Error', descripcion: 'Usuario o contraseña incorrecta', autoCierre: true });
            } else if (tipo === 'error') {
                agregarToast({ tipo: 'error2', titulo: 'Error', descripcion: 'La cédula debe contener entre 7 y 8 dígitos.', autoCierre: true });
            } else if (tipo === 'info') {
                agregarToast({ tipo: 'info', titulo: 'Información', descripcion: 'Por favor, asegúrese de que la fecha y hora de su ordenador estén configuradas correctamente para que el sistema funcione adecuadamente.'});
            } else if (tipo === 'alert') {
                agregarToast({ tipo: 'alert', titulo: 'Alerta', descripcion: 'Ya se registró la entrada de este trabajador hoy.'});
            } else if (tipo === 'alert') {
                agregarToast({ tipo: 'alert', titulo: 'Alerta', descripcion: 'Ya se registró la salida de este trabajador hoy.'});
            }
        });
    }

    if (contenedorToast) {
        contenedorToast.addEventListener('click', (e) => {
            if (e.target.closest('button.btn-cerrar')) {
                const toastId = e.target.closest('div.toast')?.id;
                cerrarToast(toastId);
            }
        });
    }
});

// Define la función cerrarToast después
const cerrarToast = (id) => {
    const toast = document.getElementById(id);
    if (toast) {
        toast.classList.add('cerrando');
    }
};
