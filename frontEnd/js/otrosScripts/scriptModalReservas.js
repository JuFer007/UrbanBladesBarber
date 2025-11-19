document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("modalReservaNueva");
  const btnCerrar = document.getElementById("btnCerrarModalReserva");
  const btnCancelar = document.getElementById("btnCancelarReserva");
  const checkNuevoCliente = document.getElementById("checkNuevoClienteReserva");
  const camposNuevoCliente = document.getElementById("camposNuevoClienteReserva");
  const form = document.getElementById("formReservaNueva");
  const selectCliente = document.getElementById("clienteReserva");
  const selectServicio = document.getElementById("servicioReserva");
  const selectBarbero = document.getElementById("barberoReserva");
  const sistemaToast = new SistemaToast(); 

  //Abrir modal
  function abrirModal() {
    modal.style.display = "flex";
    document.body.style.overflow = "hidden";
    cargarClientes();
    cargarServicios();
  }

  //Cerrar modal
  function cerrarModal() {
    modal.style.display = "none";
    document.body.style.overflow = "auto";
    checkNuevoCliente.checked = false;
    camposNuevoCliente.style.display = "none";
    selectCliente.disabled = false;
    form.reset();
  }

  checkNuevoCliente.addEventListener("change", () => {
    camposNuevoCliente.style.display = checkNuevoCliente.checked ? "block" : "none";
    selectCliente.disabled = checkNuevoCliente.checked;
  });

  //Botones
  document.getElementById("btnNuevaCita")?.addEventListener("click", abrirModal);
  btnCerrar.addEventListener("click", cerrarModal);
  btnCancelar.addEventListener("click", cerrarModal);
  modal.addEventListener("click", (e) => { if (e.target === modal) cerrarModal(); });

  //Cargar clientes
  function cargarClientes() {
    fetch('../../backEnd/controladores/controladorCliente.php')
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          selectCliente.innerHTML = '<option value="" selected disabled>Seleccione un cliente...</option>';
          data.clientes.forEach(cliente => {
            const option = document.createElement("option");
            option.value = cliente.id;
            option.textContent = `${cliente.nombre} ${cliente.apellidoP} ${cliente.apellidoM}`;
            selectCliente.appendChild(option);
          });
        }
      })
      .catch(err => console.error("Error fetch clientes:", err));
  }

  //Cargar servicios
  function cargarServicios() {
    fetch('../../backEnd/controladores/controladorServicios.php?formato=json')
      .then(res => res.json())
      .then(data => {
        selectServicio.innerHTML = '<option value="" selected disabled>Seleccione un servicio...</option>';
        data.forEach(servicio => {
          const option = document.createElement("option");
          option.value = servicio.id;
          option.textContent = servicio.nombre;
          selectServicio.appendChild(option);
        });
      })
      .catch(err => console.error("Error cargando servicios:", err));
  }

  //Enviar formulario
  form.addEventListener("submit", (e) => {
    e.preventDefault();

    let url;
    const datos = {
      idServicio: selectServicio.value,
      idBarbero: null,
      fechaReserva: form.fechaReserva.value,
      hora: form.horaReserva.value,
      estado: ""
    };

    if (checkNuevoCliente.checked) {
      //Cliente nuevo
      datos.nombre = form.nombreReserva.value;
      const apellidos = form.apellidosReserva.value.split(" ");
      datos.apellidoPaterno = apellidos[0] || "";
      datos.apellidoMaterno = apellidos[1] || "";
      datos.telefono = form.telefonoReserva.value;
      datos.email = form.emailReserva?.value || "";

      if (datos.apellidoMaterno = null) {
        sistemaToast.mostrar('warning', 'Advertencia de datos', 'Ingrese los dos apellidos del cliente');
      }  

      url = '../../backEnd/controladores/controladorAgregarReserva.php';
    } else {
      //Cliente existente
      datos.idCliente = selectCliente.value;
      url = '../../backEnd/controladores/controladorNuevaReserva.php';
    }

    fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(datos)
    })
    .then(res => res.json())
    .then(resp => {
      if(resp.success) {
        cerrarModal();
        sistemaToast.mostrar('success', 'Reserva registrada', 'La reserva se registro correctamente.');
        document.dispatchEvent(new CustomEvent('reservaAgregada'));
      } else {
        console.log("Error: " + resp.mensaje);
      }
    })
    .catch(err => console.error("Error al guardar reserva:", err));
  });
});