document.addEventListener("DOMContentLoaded", async () => {
  // --- DOM ---
  const filtroBarberos = document.getElementById("filtroBarberos");
  const calendarioGrid = document.getElementById("calendarioGrid");
  const mesAnioSpan = document.getElementById("mesAnio");
  const btnMesAnterior = document.getElementById("mesAnterior");
  const btnMesSiguiente = document.getElementById("mesSiguiente");
  const contenedorHorarios = document.getElementById("contenedorHorarios");
  const fechaMostrada = document.getElementById("fechaMostrada");
  const sistemaToast = new SistemaToast();

  // Modal editar reserva
  const modalEditar = document.getElementById("modalEditarReserva");
  const formEditar = document.getElementById("formEditarReserva");
  const btnCerrarModalEditar = document.getElementById("btnCerrarModalEditarReserva");
  const btnCancelarModalEditar = document.getElementById("btnCancelarModalEditarReserva");
  const inputFechaReserva = document.getElementById("fechaEditar");
  const inputHoraReserva = document.getElementById("horaEditar");

  // Modal de confirmación para cancelar
  const modalConfirmarCancelacion = document.getElementById("modalConfirmarCancelacion");
  const btnConfirmarCancelacion = document.getElementById("btnConfirmarCancelacion");
  const btnRechazarCancelacion = document.getElementById("btnRechazarCancelacion");

  // --- Estado ---
  let fechaActual = new Date();
  let fechaSeleccionada = new Date();
  let barberos = [];
  let todasLasReservas = [];
  let reservaEditando = null;
  let reservaParaCancelarId = null;

  // --- Inicializar ---
  await cargarBarberos();
  renderizarCalendario();
  await cargarReservas();

  // --- Eventos ---
  btnMesAnterior.addEventListener("click", () => {
    fechaActual.setMonth(fechaActual.getMonth() - 1);
    renderizarCalendario();
  });

  btnMesSiguiente.addEventListener("click", () => {
    fechaActual.setMonth(fechaActual.getMonth() + 1);
    renderizarCalendario();
  });

  filtroBarberos.addEventListener("change", () => mostrarReservas(todasLasReservas));

  // Cerrar y cancelar modal
  btnCerrarModalEditar.addEventListener("click", () => cerrarModalEditar());
  btnCancelarModalEditar.addEventListener("click", () => cerrarModalEditar());

  // Eventos del modal de confirmación de cancelación
  btnRechazarCancelacion.addEventListener("click", () => {
    modalConfirmarCancelacion.style.display = "none";
    reservaParaCancelarId = null;
  });

  btnConfirmarCancelacion.addEventListener("click", async () => {
    if (!reservaParaCancelarId) return;

    try {
      await ejecutarCancelacion(reservaParaCancelarId);
    } catch (err) {
      console.error("Error al confirmar cancelación:", err);
      sistemaToast.mostrar("error", "Ocurrió un error inesperado.");
    } finally {
      modalConfirmarCancelacion.style.display = "none";
      reservaParaCancelarId = null;
    }
  });

  // Escuchar evento personalizado para recargar las reservas
  document.addEventListener('reservaAgregada', async () => {
    await cargarReservas();
  });
  // --- Modal ---
  function abrirModalEditarReserva(reserva) {
    reservaEditando = reserva;
    inputFechaReserva.value = reserva.fechaReserva || formatearFecha(new Date());
    inputHoraReserva.value = reserva.hora ? reserva.hora.substring(0, 5) : "";
    modalEditar.style.display = "flex";
  }

  function cerrarModalEditar() {
    modalEditar.style.display = "none";
    formEditar.reset();
    reservaEditando = null;
  }

  // --- Guardar cambios (editar reserva) ---
  formEditar.addEventListener("submit", async (e) => {
    e.preventDefault();
    
    console.log("=== INICIO EDICIÓN ===");
    console.log("Reserva editando:", reservaEditando);
    
    if (!reservaEditando) {
      console.error("No hay reserva seleccionada");
      return;
    }

    const nuevaFecha = inputFechaReserva.value;
    const nuevaHora = inputHoraReserva.value;

    if (!nuevaFecha || !nuevaHora) {
      sistemaToast('warning', 'Debe completar todos los campos');
      return;
    }

    //Validar formato de fecha
    if (!/^\d{4}-\d{2}-\d{2}$/.test(nuevaFecha)) {
      alert("Formato de fecha inválido. Use YYYY-MM-DD");
      return;
    }

    //Validar formato de hora (HH:MM)
    if (!/^\d{2}:\d{2}$/.test(nuevaHora)) {
      alert("Formato de hora inválido. Use HH:MM");
      return;
    }

    const payload = {
      idReserva: reservaEditando.idReserva,
      fechaReserva: nuevaFecha,
      hora: nuevaHora
    };

    try {
      const url = "../../backEnd/controladores/controladorEditarDatosReserva.php";

      const res = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });

      //Verificar si la respuesta es JSON válida
      const contentType = res.headers.get("content-type");

      if (!contentType || !contentType.includes("application/json")) {
        return;
      }

      const data = await res.json();
      console.log("Datos recibidos del servidor:", data);

      if (data.success) {
        sistemaToast.mostrar("success", "Reserva actualizada correctamente");
        cerrarModalEditar();
        await cargarReservas();
      } else {
        sistemaToast.mostrar("warning", "Error al actualizar la reserva");
      }
    } catch (err) {
      console.error("Error completo:", err);
      console.error("Stack trace:", err.stack);
    }
  });

  // --- Cancelar reserva ---
  async function cancelarReserva(id) {
    reservaParaCancelarId = id;
    modalConfirmarCancelacion.style.display = "flex";
  }

  async function ejecutarCancelacion(id) {
    try {
      const res = await fetch("../../backEnd/controladores/controladorCancelarReserva.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ idReserva: id }),
      });
      const data = await res.json();
      if (data.success) {
        sistemaToast.mostrar("success", "Reserva cancelada correctamente");
        await cargarReservas();
      } else {
        sistemaToast.mostrar("error", data.mensaje || "No se pudo cancelar la reserva.");
      }
    } catch (err) {
      console.error("Error cancelando reserva:", err);
      sistemaToast.mostrar("error", "Error de conexión al cancelar.");
    }
  }  

  // --- Barberos y Reservas ---
  async function cargarBarberos() {
    try {
      const res = await fetch("../../backEnd/controladores/controladorReservas.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ accion: "listarBarberos" }),
      });
      const data = await res.json();

      if (data.success && Array.isArray(data.data)) {
        barberos = data.data;
        filtroBarberos.innerHTML = '<option value="">Todos los barberos</option>';
        barberos.forEach((b) => {
          const o = document.createElement("option");
          o.value = b;
          o.textContent = b;
          filtroBarberos.appendChild(o);
        });
      }
    } catch (err) {
      console.error("Error cargarBarberos:", err);
    }
  }

  async function cargarReservas() {
    try {
      const fecha = formatearFecha(fechaSeleccionada);
      const res = await fetch("../../backEnd/controladores/controladorReservas.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ accion: "listarReservasPorFecha", fecha }),
      });

      const data = await res.json();
      todasLasReservas = data.success && Array.isArray(data.data) ? data.data : [];
      actualizarFechaMostrada();
      mostrarReservas(todasLasReservas);
    } catch (err) {
      console.error("Error cargarReservas:", err);
      todasLasReservas = [];
      mostrarReservas([]);
    }
  }

  function mostrarReservas(reservas) {
    contenedorHorarios.innerHTML = "";
    const filtro = filtroBarberos.value;

    if (!reservas || reservas.length === 0) {
      contenedorHorarios.innerHTML =
        '<div class="mensaje-vacio"><i class="fa-solid fa-box-open"></i> No hay reservas para este día</div>';
      return;
    }

    const resFiltradas = filtro ? reservas.filter((r) => r.Barbero === filtro) : reservas;

    if (resFiltradas.length === 0) {
      contenedorHorarios.innerHTML =
        '<div class="mensaje-vacio"><i class="fa-solid fa-box-open"></i> No hay reservas para este barbero</div>';
      return;
    }

    const agrup = {};
    resFiltradas.forEach((r) => {
      agrup[r.Barbero] = agrup[r.Barbero] || [];
      agrup[r.Barbero].push(r);
    });

    Object.entries(agrup).forEach(([barbero, lista]) => {
      const bloque = document.createElement("div");
      bloque.className = "bloque-barbero";
      bloque.innerHTML = `<h3><i class="fa-solid fa-scissors"></i> ${barbero}</h3>`;

      const tabla = document.createElement("table");
      tabla.className = "tabla-reservas";
      tabla.innerHTML = `
        <thead>
          <tr>
            <th>Cliente</th>
            <th>Hora</th>
            <th>Servicio</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
      `;

      const tbody = document.createElement("tbody");
      lista
        .sort((a, b) => a.hora.localeCompare(b.hora))
        .forEach((r) => {
          const tr = document.createElement("tr");
          const estadoLower = String(r.Estado || "").toLowerCase();
          let clase = estadoLower.includes("complet")
            ? "estado-completada"
            : estadoLower.includes("cancel")
            ? "estado-cancelada"
            : estadoLower.includes("pend")
            ? "estado-pendiente"
            : "estado-confirmada";

          tr.innerHTML = `
            <td>${r.Cliente}</td>
            <td>${r.hora.substring(0, 5)}</td>
            <td>${r.nombreServicio || "Servicio"}</td>
            <td><span class="estado-td ${clase}">${r.Estado || ""}</span></td>
            <td></td>
          `;

          const tdAcciones = tr.querySelector("td:last-child");
          if (!estadoLower.includes("complet") && !estadoLower.includes("cancel")) {
            const btnE = document.createElement("button");
            btnE.className = "btn-accion btn-editar";
            btnE.textContent = "✎ Editar";
            btnE.addEventListener("click", () => {
              abrirModalEditarReserva(r);
            });

            const btnC = document.createElement("button");
            btnC.className = "btn-accion btn-cancelar";
            btnC.textContent = "✖ Cancelar";
            btnC.addEventListener("click", () => cancelarReserva(r.idReserva));

            tdAcciones.appendChild(btnE);
            tdAcciones.appendChild(btnC);
          }

          tbody.appendChild(tr);
        });

      tabla.appendChild(tbody);
      bloque.appendChild(tabla);
      contenedorHorarios.appendChild(bloque);
    });
  }

  // --- Calendario ---
  function renderizarCalendario() {
    const meses = [
      "Enero",
      "Febrero",
      "Marzo",
      "Abril",
      "Mayo",
      "Junio",
      "Julio",
      "Agosto",
      "Septiembre",
      "Octubre",
      "Noviembre",
      "Diciembre",
    ];

    mesAnioSpan.textContent = `${meses[fechaActual.getMonth()]} ${fechaActual.getFullYear()}`;
    calendarioGrid.querySelectorAll(".dia").forEach((d) => d.remove());

    const primerDia = new Date(fechaActual.getFullYear(), fechaActual.getMonth(), 1);
    const ultimoDia = new Date(fechaActual.getFullYear(), fechaActual.getMonth() + 1, 0);

    for (let i = 0; i < primerDia.getDay(); i++) {
      calendarioGrid.appendChild(Object.assign(document.createElement("div"), { className: "dia inactivo" }));
    }

    for (let d = 1; d <= ultimoDia.getDate(); d++) {
      const el = document.createElement("div");
      el.className = "dia";
      el.textContent = d;
      if (esMismoDia(d, fechaActual, fechaSeleccionada)) el.classList.add("activo");
      el.addEventListener("click", async () => {
        fechaSeleccionada = new Date(fechaActual.getFullYear(), fechaActual.getMonth(), d);
        renderizarCalendario();
        await cargarReservas();
      });
      calendarioGrid.appendChild(el);
    }

    actualizarFechaMostrada();
  }

  function actualizarFechaMostrada() {
    fechaMostrada.textContent = fechaSeleccionada.toLocaleDateString("es-ES", {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    });
  }

  function formatearFecha(fecha) {
    return `${fecha.getFullYear()}-${String(fecha.getMonth() + 1).padStart(2, "0")}-${String(
      fecha.getDate()
    ).padStart(2, "0")}`;
  }

  function esMismoDia(d, fechaMes, fechaComparar) {
    return (
      d === fechaComparar.getDate() &&
      fechaMes.getMonth() === fechaComparar.getMonth() &&
      fechaMes.getFullYear() === fechaComparar.getFullYear()
    );
  }
});
