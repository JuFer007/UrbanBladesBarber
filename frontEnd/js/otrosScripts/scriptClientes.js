document.addEventListener("DOMContentLoaded", () => {
    const sistemaToast = new SistemaToast();
    const seccionClientes = document.getElementById('divClientes');

    const clientesBody = document.getElementById('clientes-body');
    const reservasBody = document.getElementById('reservas-body');
    const buscarInput = document.getElementById('buscarCliente');

    const modalAgregar = document.getElementById("modalAgregarCliente");
    const modalEditar = document.getElementById("modalEditarCliente");
    const btnAbrirModalCliente = document.getElementById("btnAbrirModalCliente");
    const btnEditarCliente = document.getElementById("btnEditarCliente");

    // Inputs Agregar
    const inputAgregarNombre = modalAgregar.querySelector("input[placeholder='Ingrese nombre']");
    const inputAgregarApellidos = modalAgregar.querySelector("input[placeholder='Ingrese apellidos']");
    const inputAgregarTelefono = modalAgregar.querySelector("input[placeholder='Ingrese teléfono']");
    const inputAgregarEmail = modalAgregar.querySelector("input[placeholder='Ingrese email']");

    // Inputs Editar
    const inputEditarNombre = modalEditar.querySelector("input[readonly][value='Nombre cliente']");
    const inputEditarApellidos = modalEditar.querySelector("input[readonly][value='Apellidos cliente']");
    const inputEditarTelefono = modalEditar.querySelector("input[placeholder='Actualizar teléfono']");
    const inputEditarEmail = modalEditar.querySelector("input[placeholder='Actualizar email']");

    let clientes = [];
    let clienteSeleccionado = null;
    let clientesCargados = false;

    // ---------------- Cargar reservas ----------------
    function cargarReservas(idCliente) {
        fetch(`../../backEnd/controladores/controladorCliente.php?idCliente=${idCliente}`)
        .then(res => res.json())
        .then(data => {
            if(data.success){
                const reservas = data.reservas;
                if(reservas.length > 0){
                    reservasBody.innerHTML = reservas.map(r => `
                        <tr>
                            <td style="padding:8px;">${r.nombreServicio}</td>
                            <td style="padding:8px;">${r.fechaReserva}</td>
                            <td style="padding:8px;">${r.hora}</td>
                            <td style="padding:8px;">$${r.precio}</td>
                        </tr>
                    `).join('');
                } else {
                    reservasBody.innerHTML = `<tr><td colspan="4" style="text-align:center; padding:10px;">No hay reservas</td></tr>`;
                }
            } else {
                reservasBody.innerHTML = `<tr><td colspan="4" style="text-align:center; padding:10px;">Error: ${data.mensaje}</td></tr>`;
                sistemaToast.mostrar("error", `Error al cargar reservas: ${data.mensaje}`);
            }
        })
        .catch(err => {
            reservasBody.innerHTML = `<tr><td colspan="4" style="text-align:center; padding:10px;">Error de conexión: ${err}</td></tr>`;
            sistemaToast.mostrar("error", `Error de conexión: ${err}`);
            console.error(err);
        });
    }

    // ---------------- Renderizar clientes ----------------
    function renderClientes(clientesFiltrados = null) {
        const lista = clientesFiltrados || clientes;
        clientesBody.innerHTML = lista.map(c => `
            <tr data-id="${c.id}">
                <td style="padding:8px;">${c.nombre}</td>
                <td style="padding:8px;">${c.apellidoP}</td>
                <td style="padding:8px;">${c.apellidoM}</td>
                <td style="padding:8px;">${c.telefono}</td>
                <td style="padding:8px;">${c.email}</td>
            </tr>
        `).join('');

        clientesBody.querySelectorAll('tr').forEach(fila => {
            fila.addEventListener('click', () => {
                clientesBody.querySelectorAll('tr').forEach(r => r.classList.remove('seleccionado'));
                fila.classList.add('seleccionado');
                clienteSeleccionado = lista.find(cli => cli.id == fila.dataset.id);
                cargarReservas(fila.dataset.id);
            });
        });

        // Seleccionar primera fila por defecto
        const primeraFila = clientesBody.querySelector('tr');
        if(primeraFila){
            primeraFila.classList.add('seleccionado');
            clienteSeleccionado = lista.find(cli => cli.id == primeraFila.dataset.id);
            cargarReservas(primeraFila.dataset.id);
        }
    }

    // ---------------- Filtrado ----------------
    buscarInput.addEventListener('input', () => {
        const texto = buscarInput.value.toLowerCase();
        const filtrados = clientes.filter(c =>
            c.nombre.toLowerCase().includes(texto) ||
            c.apellidoP.toLowerCase().includes(texto) ||
            c.apellidoM.toLowerCase().includes(texto) ||
            c.telefono.includes(texto) ||
            c.email.toLowerCase().includes(texto)
        );
        renderClientes(filtrados);
    });

    // ---------------- Traer clientes ----------------
    async function cargarClientes() {
        if (clientesCargados) return; 
        try {
            const res = await fetch('../../backEnd/controladores/controladorCliente.php');
            const data = await res.json();
            if(data.success){
                clientes = data.clientes;
                renderClientes();
            } else {
                clientesCargados = true; // Marcar como cargado para no reintentar infinitamente
                clientesBody.innerHTML = `<tr><td colspan="5">Error al cargar clientes: ${data.mensaje}</td></tr>`;
                sistemaToast.mostrar("error", `Error al cargar clientes: ${data.mensaje}`);
                console.error(data.mensaje);
            }
        } catch(err){
            clientesBody.innerHTML = `<tr><td colspan="5">Error de conexión: ${err}</td></tr>`;
            sistemaToast.mostrar("error", `Error de conexión: ${err}`);
            console.error(err);
        }
    }

    // ---------------- Abrir modales ----------------
    btnAbrirModalCliente.addEventListener("click", () => {
        modalAgregar.classList.add("show");
        inputAgregarNombre.value = "";
        inputAgregarApellidos.value = "";
        inputAgregarTelefono.value = "";
        inputAgregarEmail.value = "";
    });

    btnEditarCliente.addEventListener("click", () => {
        if (!clienteSeleccionado) {
            sistemaToast.mostrar("warning", "Debe seleccionar un cliente de la tabla antes de editar.");
            return;
        }
        inputEditarNombre.value = clienteSeleccionado.nombre;
        inputEditarApellidos.value = clienteSeleccionado.apellidoP + " " + clienteSeleccionado.apellidoM;
        inputEditarTelefono.value = clienteSeleccionado.telefono;
        inputEditarEmail.value = clienteSeleccionado.email;
        modalEditar.classList.add("show");
    });

    // ---------------- Cerrar modales ----------------
    document.querySelectorAll("[data-close]").forEach(btn => {
        btn.addEventListener("click", () => {
            const modalId = btn.getAttribute("data-close");
            document.getElementById(modalId).classList.remove("show");
        });
    });

    window.addEventListener("click", (e) => {
        if (e.target.classList.contains("custom-modal")) {
            e.target.classList.remove("show");
        }
    });

    // ---------------- Agregar Cliente ----------------
    modalAgregar.querySelector("form").addEventListener("submit", async (e) => {
        e.preventDefault();
        const data = {
            nombre: inputAgregarNombre.value,
            apellidoP: inputAgregarApellidos.value.split(" ")[0] || "",
            apellidoM: inputAgregarApellidos.value.split(" ")[1] || "",
            telefono: inputAgregarTelefono.value,
            email: inputAgregarEmail.value
        };
        try {
            const res = await fetch('../../backEnd/controladores/controladorCliente.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if(result.success){
                modalAgregar.classList.remove("show");
                sistemaToast.mostrar("success", "Cliente agregado correctamente");
                cargarClientes(); 
            } else {
                sistemaToast.mostrar("error", result.mensaje || "Error al agregar cliente");
                console.error(result.mensaje);
            }
        } catch(err){
            sistemaToast.mostrar("error", `Error de conexión: ${err}`);
            console.error(err);
        }
    });

    // ---------------- Editar Cliente ----------------
    modalEditar.querySelector("form").addEventListener("submit", async (e) => {
        e.preventDefault();
        if(!clienteSeleccionado){
            sistemaToast.mostrar("warning", "No hay cliente seleccionado.");
            return;
        }
        const data = {
            nombre: clienteSeleccionado.nombre,
            apellidoP: clienteSeleccionado.apellidoP,
            apellidoM: clienteSeleccionado.apellidoM,
            telefono: inputEditarTelefono.value,
            email: inputEditarEmail.value,
            apellidoPActual: clienteSeleccionado.apellidoP,
            apellidoMActual: clienteSeleccionado.apellidoM
        };
        try {
            const res = await fetch('../../backEnd/controladores/controladorEditarCliente.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if(result.success){
                modalEditar.classList.remove("show");
                sistemaToast.mostrar("success", "Datos de cliente actualizados correctamente");
                cargarClientes(); 
            } else {
                sistemaToast.mostrar("error", result.mensaje || "Error al actualizar cliente");
                console.error(result.mensaje);
            }
        } catch(err){
            sistemaToast.mostrar("error", `Error de conexión: ${err}`);
            console.error(err);
        }
    });

    // ---------------- Inicializar ----------------
    const observer = new MutationObserver((mutations) => {
        mutations.forEach(mutation => {
            
            if (mutation.attributeName === 'style' && seccionClientes.style.display === 'block') {
                cargarClientes();
            }
        });
    });
    observer.observe(seccionClientes, { attributes: true });
});
