let fechaActual = new Date();
let fechaSeleccionada = new Date();
let citas = []; 

const nombresMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
const nombresDias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
const sistemaToast = new SistemaToast();

// ----------------------------
// Funciones del calendario
// ----------------------------
function actualizarMesAnio() {
    document.getElementById('monthYearDisplay').textContent = 
        `${nombresMeses[fechaActual.getMonth()]} ${fechaActual.getFullYear()}`;
}

function dibujarEncabezadoDias() {
    const encabezado = document.getElementById('daysHeader');
    encabezado.innerHTML = '';
    nombresDias.forEach(dia => {
        const div = document.createElement('div');
        div.textContent = dia;
        encabezado.appendChild(div);
    });
}

function obtenerDiasDelMes(fecha) {
    const anio = fecha.getFullYear();
    const mes = fecha.getMonth();
    const primerDia = new Date(anio, mes, 1);
    const ultimoDia = new Date(anio, mes + 1, 0);
    return {
        diasDelMes: ultimoDia.getDate(),
        primerDiaSemana: primerDia.getDay()
    };
}

function obtenerCitasPorDia(dia) {
    const fechaStr = `${fechaActual.getFullYear()}-${String(fechaActual.getMonth() + 1).padStart(2,'0')}-${String(dia).padStart(2,'0')}`;
    return citas.filter(cita => cita.fechaReserva === fechaStr);
}

function dibujarCalendario() {
    const grid = document.getElementById('calendarGrid');
    grid.innerHTML = '';

    const { diasDelMes, primerDiaSemana } = obtenerDiasDelMes(fechaActual);

    for (let i = 0; i < primerDiaSemana; i++) {
        const divVacio = document.createElement('div');
        grid.appendChild(divVacio);
    }

    for (let dia = 1; dia <= diasDelMes; dia++) {
        const boton = document.createElement('button');
        boton.className = 'calendar-day';
        
        const citasDelDia = obtenerCitasPorDia(dia);
        const esSeleccionado = fechaSeleccionada.getDate() === dia &&
                               fechaSeleccionada.getMonth() === fechaActual.getMonth() &&
                               fechaSeleccionada.getFullYear() === fechaActual.getFullYear();
        const esHoy = new Date().getDate() === dia &&
                      new Date().getMonth() === fechaActual.getMonth() &&
                      new Date().getFullYear() === fechaActual.getFullYear();

        if (esSeleccionado) boton.classList.add('selected');
        if (esHoy && !esSeleccionado) boton.classList.add('today');

        boton.innerHTML = `
            <div>${dia}</div>
            ${citasDelDia.length > 0 ? 
                `<div class="appointments-count">${citasDelDia.length} cita${citasDelDia.length > 1 ? 's' : ''}</div>` 
                : ''}
        `;

        boton.onclick = () => seleccionarDia(dia);
        grid.appendChild(boton);
    }
    actualizarMesAnio();
    mostrarCitas();
}

function seleccionarDia(dia) {
    fechaSeleccionada = new Date(fechaActual.getFullYear(), fechaActual.getMonth(), dia);
    dibujarCalendario();
}

function mostrarCitas() {
    const lista = document.getElementById('appointmentsList');
    const titulo = document.getElementById('appointmentsTitle');
    
    titulo.textContent = `Citas del ${fechaSeleccionada.getDate()}/${fechaSeleccionada.getMonth() + 1}`;
    
    const citasDelDia = obtenerCitasPorDia(fechaSeleccionada.getDate());
    
    if (citasDelDia.length === 0) {
        lista.innerHTML = `
            <div style="text-align:center; padding:32px; color:#64748b;">
                <p>No hay citas programadas</p>
            </div>
        `;
    } else {
        lista.innerHTML = '';
        citasDelDia.forEach(cita => {
            const tarjeta = document.createElement('div');
            tarjeta.className = 'appointment-card';

            const esCompletada = cita.estado === "Completada";

            tarjeta.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 6px;">
                    <div style="display: flex; align-items: center; gap: 6px; font-weight: bold; color: #1e293b; font-size: 15px;">
                        <i class="fa-solid fa-clock"></i>
                        ${cita.hora}
                    </div>
                    <button 
                        ${esCompletada ? "disabled" : `onclick="completarCita(${cita.idReserva})"`} 
                        style="
                            background: ${esCompletada ? '#60a5fa' : '#71bd79'}; 
                            color: #000000; 
                            border: none; 
                            padding: 8px 16px; 
                            border-radius: 4px; 
                            font-size: 14px; 
                            font-weight: 600; 
                            transition: background 0.2s; 
                            display: flex; 
                            align-items: center; 
                            gap: 5px;
                            cursor: ${esCompletada ? 'not-allowed' : 'pointer'};
                            opacity: ${esCompletada ? '0.7' : '1'};
                        ">
                        <i class="fa-solid ${esCompletada ? 'fa-circle-info' : 'fa-check'}"></i>
                        ${esCompletada ? 'Completada' : 'Completar'}
                    </button>
                </div>
                <div style="display: flex; flex-direction: column; gap: 3px; font-size: 14px;">
                    <div style="display: flex; align-items: center; gap: 6px; font-weight: bold; color: #1e293b;">
                        <i class="fa-solid fa-user"></i>
                        <span>${cita.nombreCliente} ${cita.Apellidos}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; font-weight: bold; color: #1e293b;">
                        <i class="fa-solid fa-scissors"></i>
                        ${cita.nombreServicio}
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px; font-weight: bold; color: #1e293b;">
                        <i class="fa-solid fa-calendar-days"></i>
                        ${cita.fechaReserva}
                    </div>
                </div>
            `;
            lista.appendChild(tarjeta);
        });
    }
}

function cambiarMes(direccion) {
    fechaActual.setMonth(fechaActual.getMonth() + direccion);
    dibujarCalendario();
}

// ----------------------------
// Cargar reservas del barbero desde el servidor
// ----------------------------
function cargarReservasBarbero() {
    const idBarbero = sessionStorage.getItem('idBarbero');
    if (!idBarbero) {
        console.warn("No se encontró el id del barbero en sessionStorage");
        return;
    }

    fetch('../../backEnd/controladores/controladorListarReservasDeBarbero.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `idBarbero=${encodeURIComponent(idBarbero)}`
    })
    .then(res => res.json())
    .then(data => {
        console.log("JSON de reservas:", data);
        if (!data.error && data.reservas) {
            citas = data.reservas;
            dibujarCalendario();
            console.log("No se encontraron reservas:", data.mensaje || '');
        }
    })
    .catch(err => console.error("Error al cargar reservas:", err));
}

function completarCita(idReserva) {
    const cita = citas.find(c => c.idReserva === idReserva);
    if (!cita) {
        console.log("No se encontró la cita seleccionada");
        return;
    }

    const payload = {
        idReserva: cita.idReserva,
        nombreServicio: cita.nombreServicio,
        metodo: null
    };

    fetch('../../backEnd/controladores/controladorEstadoYPago.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        console.log("Respuesta completar cita:", data);

        if (data.success) {
            sistemaToast.mostrar('success', 'Cita Completada', 'La cita ha sido marcada como completada.');
            cita.estado = "Completada";
            cargarReservasBarbero();
        } else {
            console.log(data.mensaje);
        }
    })
    .catch(err => {
        console.error("Error en completar cita:", err);
    });
}

// ----------------------------
// Inicializar calendario
// ----------------------------
dibujarEncabezadoDias();
cargarReservasBarbero();
