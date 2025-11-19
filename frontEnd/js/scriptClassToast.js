class SistemaToast {
    constructor() {
        this.contenedor = document.getElementById("toastContainer");
        if (!this.contenedor) {
            this.contenedor = document.createElement("div");
            this.contenedor.id = "toastContainer";
            document.body.appendChild(this.contenedor);
        }
    }

    mostrar(tipo, titulo, mensaje = "") {
        const toast = document.createElement("div");
        toast.className = `toast ${tipo}`;

        let icono = "fa-circle-info";
        if (tipo === "success") icono = "fa-circle-check";
        else if (tipo === "error") icono = "fa-circle-xmark";
        else if (tipo === "warning") icono = "fa-triangle-exclamation";

        toast.innerHTML = `
            <i class="fa-solid ${icono} icono"></i>
            <div class="contenido">
                <div class="titulo">${titulo}</div>
                <div class="mensaje">${mensaje}</div>
            </div>
        `;

        this.contenedor.appendChild(toast);
        setTimeout(() => this.cerrar(toast), 4000);
    }

    cerrar(toast) {
        toast.classList.add("removing");
        toast.addEventListener("animationend", () => toast.remove());
    }
}
window.SistemaToast = SistemaToast;
