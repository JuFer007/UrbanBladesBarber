document.addEventListener("DOMContentLoaded", () => {
    //Inicializar los toast
    const sistemaToast = new SistemaToast();

    //para recargar la tabla
    function recargarTablaEmpleados() {
        fetch('../../backEnd/controladores/controladorEmpleado.php')
            .then(response => response.text())
            .then(html => {
                document.getElementById('empleados-body').innerHTML = html;
            })
            .catch(err => console.error('Error recargando empleados:', err));
    }

    //Abrir y cerrar modal
    const modal = document.getElementById("modalAgregarEmpleado");
    const btnAbrirModal = document.getElementById("btnAbrirModal");
    const btnCerrarModal = document.getElementById("btnCerrarModal");
    const btnCancelar = document.getElementById("btnCancelar");

    btnAbrirModal.addEventListener("click", () => modal.classList.add("show"));
    btnCerrarModal.addEventListener("click", () => modal.classList.remove("show"));
    btnCancelar.addEventListener("click", () => modal.classList.remove("show"));
    window.addEventListener("click", e => {
        if (e.target === modal) modal.classList.remove("show");
    });

    //Mostrar campos extra según el cargo seleccionado
    const cargoSelect = document.getElementById("cargo");
    const extraCampos = document.getElementById("extraCampos");

    cargoSelect.addEventListener("change", () => {
        const cargo = cargoSelect.value;
        extraCampos.innerHTML = "";

        if (cargo === "Barbero") {
            extraCampos.innerHTML = `
                <label for="especialidad">Especialidad</label>
                <input type="text" id="especialidad" name="especialidad" required>
            `;
        } else if (cargo === "Recepcionista") {
            extraCampos.innerHTML = `
                <label for="turno">Turno</label>
                <select id="turno" name="turno" required>
                    <option value="" selected disabled>Seleccione turno...</option>
                    <option value="Mañana">Mañana</option>
                    <option value="Tarde">Tarde</option>
                    <option value="Noche">Noche</option>
                </select>
            `;
        }
    });

    //Enviar los datos del formulario
    const formEmpleado = document.getElementById("formEmpleado");
    formEmpleado.addEventListener("submit", (e) => {
        e.preventDefault();

        const formData = new FormData(formEmpleado);
        const datosEmpleados = Object.fromEntries(formData.entries());

        if (datosEmpleados.cargo === "Barbero") {
            datosEmpleados.especialidad = document.getElementById("especialidad").value;
        } else if (datosEmpleados.cargo === "Recepcionista") {
            datosEmpleados.turno = document.getElementById("turno").value;
        }

        fetch('../../backEnd/controladores/controladorAgregarEmpleado.php', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datosEmpleados)
        })
        .then(res => res.text())
        .then(texto => {
            console.log("Respuesta cruda:", texto);
            try {
                const data = JSON.parse(texto);
                if (data.success) {
                  modal.classList.remove("show");
                  formEmpleado.reset();
                  extraCampos.innerHTML = "";
                  sistemaToast.mostrar('success', 'Empleado agregado', 'Usuario y contraseña generados automáticamente.');
                  recargarTablaEmpleados(); // <-- Aquí se recarga la tabla
                } else {
                    sistemaToast.mostrar('error', 'Error al agregar empleado');
                }
            } catch (err) {
                console.error("No es JSON válido:", err);
            }
        })
        .catch(err => console.error("Error en fetch:", err));
    });
});
