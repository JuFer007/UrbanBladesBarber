//Variables gloables
let usuarioLogueado = false;
let nombreCliente = "";
let correoElectronico = "";
let fotoPerfil = "";
let idUsuario = null;
let idCliente = null;

document.addEventListener("DOMContentLoaded", () => {

//Obtener datos
const verificarSesion = async () => {
    try {
        const response = await fetch("http://localhost:8000/backEnd/servicios/obtenerSesionUsuario.php", {
            credentials: "include"
        });
        const data = await response.json();

        usuarioLogueado = data.logueado || false;
        nombreCliente = data.nombreCliente || "Usuario";
        correoElectronico = data.correoElectronico || "";
        fotoPerfil = data.fotoPerfil || "/recursos/default-avatar.png";
        idUsuario = data.idUsuario || null;
        idCliente = data.idCliente || null;

        if (!usuarioLogueado) return;

        // --- Mostrar el menú del usuario ---
        document.getElementById("login-link-pc").style.display = "none";
        document.getElementById("login-link-mobile").style.display = "none";
        document.getElementById("userMenuPC").style.display = "block";
        document.getElementById("userMenuMobile").style.display = "block";

        // --- Cargar datos en el menú de PC ---
        document.getElementById("userNamePC").textContent = nombreCliente;
        document.getElementById("userNameEmail").textContent = correoElectronico;
        document.getElementById("userAvatarPC").src = fotoPerfil;

        // --- Cargar datos en el menú móvil ---
        document.getElementById("userNameMobile").textContent = nombreCliente;
        document.getElementById("userEmailMobile").textContent = correoElectronico;
        document.getElementById("userAvatarMobile").src = fotoPerfil;

    } catch (error) {
        console.error("Error al obtener sesión:", error);
    }
};

const userAvatarPC = document.getElementById("userAvatarPC");
const userDropdownPC = document.getElementById("userDropdownPC");

if (userAvatarPC && userDropdownPC) {
    userAvatarPC.addEventListener("click", (e) => {
        e.stopPropagation();
        userDropdownPC.classList.toggle("show");
    });

    document.addEventListener("click", (e) => {
        if (!userDropdownPC.contains(e.target) && e.target !== userAvatarPC) {
            userDropdownPC.classList.remove("show");
        }
    });
}

//Para cerrar sesion
const logoutBtnPC = document.getElementById("logoutBtnPC");
const logoutBtnMobile = document.getElementById("logoutBtnMobile");

const handleLogout = async () => {
    try {
        const response = await fetch("http://localhost:8000/backEnd/servicios/cerrarSesion.php", {
            method: 'POST',
            credentials: "include"
        });
        const data = await response.json();

        if (data.success) {
            usuarioLogueado = false;
            nombreCliente = "";
            correoElectronico = "";
            fotoPerfil = "";
            idUsuario = null;
            idCliente = null;

            window.location.reload();
        } else {
            console.error("Error al cerrar sesión:", data.message);
        }
    } catch (error) {
        console.error("Error de red al cerrar sesión:", error);
    }
};

if (logoutBtnPC) logoutBtnPC.addEventListener("click", handleLogout);
if (logoutBtnMobile) logoutBtnMobile.addEventListener("click", handleLogout);

verificarSesion();
});
