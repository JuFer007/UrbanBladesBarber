const LoginForm = document.getElementById('LoginForm');

// -----------------------------
// Función para obtener perfil y guardar en localStorage
// -----------------------------
function obtenerPerfilUsuario(usuario) {
    return fetch('/backEnd/controladores/controladorDatosUsuario.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `usuario=${encodeURIComponent(usuario)}`
    })
    .then(res => {
        if (!res.ok) throw new Error(`Error HTTP: ${res.status}`);
        return res.json();
    })
    .then(data => {
        if (!data.error && data.usuario) {
            localStorage.setItem("usuarioNombre", data.usuario);
            localStorage.setItem("usuarioCargo", data.cargo);
            localStorage.setItem("usuarioGenero", data.genero);
        }
        return data;
    });
}

//obtener el id de un barbero
function obtenerIdBarbero(usuario){
    return fetch('../../backEnd/controladores/controladorIdBarbero.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `usuario=${encodeURIComponent(usuario)}`
    })
    .then(res => res.json())
    .then(data => {
        if (!data.error && data.idBarbero) {
            return data.idBarbero;
        } else {
            return null;
        }
    })
    .catch(err => {
        console.error("Error al obtener ID Barbero:", err);
        return null;
    });
}

// -----------------------------
// Mostrar datos en dashboard
// -----------------------------
document.addEventListener("DOMContentLoaded", () => {
    const nombre = localStorage.getItem("usuarioNombre");
    const cargo = localStorage.getItem("usuarioCargo");
    const genero = localStorage.getItem("usuarioGenero")?.toLowerCase() || "";

    if (nombre && cargo) {
        const profileName = document.getElementById("profile-name");
        const profileRole = document.getElementById("profile-role");
        const profileImg = document.getElementById("profile-img");

        if (profileName) profileName.textContent = nombre;
        if (profileRole) profileRole.textContent = cargo;

        // Imagen según cargo + género
        let imagen = "https://img.freepik.com/vector-premium/icono-perfil-avatar-estilo-plano-ilustracion-vector-perfil-usuario-masculino-sobre-fondo-aislado-concepto-negocio-signo-perfil-hombre_157943-38764.jpg";

        switch (cargo.toLowerCase()) {
            case "administrador":
                imagen = (genero === "femenino") 
                    ? 'https://i.imgur.com/IogPnjv.jpeg' 
                    : 'https://i.imgur.com/aXAz5J3.jpeg';
                break;
            case "recepcionista":
                imagen = (genero === "femenino") 
                    ? 'https://i.imgur.com/gHaQRGi.jpeg' 
                    : 'https://i.imgur.com/xm0ixha.jpeg';
                break;
            case "barbero":
                imagen = 'https://i.imgur.com/ofqkTNn.jpeg';    
                break;
        }

        if (profileImg) profileImg.style.backgroundImage = `url('${imagen}')`;
        console.log("Imagen asignada:", imagen); 
    }
});

// -----------------------------
// Validación login
// -----------------------------
if (LoginForm) {
    const toast = new SistemaToast();

    LoginForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const user = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

        if (!user || !password) {
            toast.mostrar('info', 'Campos incompletos', 'Por favor, ingrese su usuario y contraseña.');
            return;
        }

        fetch('/backEnd/controladores/controladorUsuario.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `usuario=${encodeURIComponent(user)}&contrasena=${encodeURIComponent(password)}`
        })
        .then(res => {
            if (!res.ok) throw new Error(`Error HTTP: ${res.status}`);
            return res.json();
        })
        .then(data => {
            if (data.valido && data.estado === "Activo") {
                toast.mostrar('success', `¡Bienvenido!`, 'Usuario y contraseña correctas.');

                obtenerPerfilUsuario(user).then(() => {
                    if (data.cargo === "Barbero") {
                        obtenerIdBarbero(user).then(idBarbero => {
                            sessionStorage.setItem('idBarbero', idBarbero);
                        });
                    }

                    setTimeout(() => {
                        window.location.href = "/frontEnd/html/dashboard.html";
                    }, 1500);
                });

            } else if (data.estado === "Inactivo") {
                toast.mostrar('warning', 'Usuario inactivo', 'Tu cuenta está deshabilitada.');
            } else {
                toast.mostrar('error', 'Error al iniciar sesión', 'El usuario o la contraseña son incorrectos.');
            }
        })
        .catch((error) => {
            console.error("Error en fetch:", error);
        });
    });
}

// -----------------------------
// Cerrar sesión
// -----------------------------
document.getElementById('LogOut')?.addEventListener("click", () => {
    localStorage.removeItem("usuarioNombre");
    localStorage.removeItem("usuarioCargo");
    localStorage.removeItem("usuarioGenero");
    window.location.href = "/index.html";
});
