document.addEventListener("DOMContentLoaded", () => {
    const reservasModal = document.getElementById("reservasModal");
    const formReserva = document.getElementById("formReserva");
    const modalSesion = new bootstrap.Modal(document.getElementById('modalSesion'));

    // --- Lógica para manejar la apertura del modal de reserva ---
    reservasModal.addEventListener('show.bs.modal', function (event) {
        if (typeof usuarioLogueado === 'undefined' || !usuarioLogueado) {
            event.preventDefault();
            modalSesion.show();
            return;
        }

        if (typeof usuarioLogueado !== 'undefined' && usuarioLogueado) {
            const nombreCompleto = nombreCliente.split(' ');
            const nombre = nombreCompleto.length > 0 ? nombreCompleto[0] : '';
            const apellidos = nombreCompleto.length > 1 ? nombreCompleto.slice(1).join(' ') : '';

            formReserva.querySelector("#nombreCliente").value = nombre;
            formReserva.querySelector("#apellidos").value = apellidos;
            formReserva.querySelector("#correoE").value = correoElectronico;

            formReserva.querySelector("#nombreCliente").readOnly = true;
            formReserva.querySelector("#apellidos").readOnly = true;
            formReserva.querySelector("#correoE").readOnly = true;
        } else {
            formReserva.reset();
            formReserva.querySelector("#nombreCliente").readOnly = false;
            formReserva.querySelector("#apellidos").readOnly = false;
            formReserva.querySelector("#correoE").readOnly = false;
        }
    });

    if (!formReserva) {
        console.error("Formulario de reserva no encontrado");
        return;
    }

    formReserva.addEventListener("submit", function (e) {
        e.preventDefault();

        if (typeof usuarioLogueado === 'undefined' || !usuarioLogueado) {
            const modalReservaInstance = bootstrap.Modal.getInstance(reservasModal);
            if (modalReservaInstance) modalReservaInstance.hide();

            setTimeout(() => modalSesion.show(), 400);
            return;
        }

        const nombre = formReserva.querySelector("#nombreCliente").value.trim();
        const apellidos = formReserva.querySelector("#apellidos").value.trim();
        const telefono = formReserva.querySelector("#telefono").value.trim();
        const correo = formReserva.querySelector("#correoE").value.trim();
        const fechaReserva = formReserva.querySelector("#fecha").value;
        const hora = formReserva.querySelector("#hora").value;
        const idServicio = formReserva.querySelector("#selectServicioModal").value;

        if (!nombre || !apellidos || !telefono || !fechaReserva || !hora || !idServicio) {
            alert('Por favor completa todos los campos requeridos.');
            return;
        }

        if (!validarHorario(hora)) {
            const modalInstance = bootstrap.Modal.getInstance(reservasModal);
            if (modalInstance) {
                modalInstance.hide();
            }

            setTimeout(() => {
                mostrarModalAdvertencia();
            }, 400);
            return;
        }

        //Dividir apellidos
        let apellidoPaterno = '';
        let apellidoMaterno = '';
        const partes = apellidos.split(' ');
        if (partes.length > 0) apellidoPaterno = partes[0];
        if (partes.length > 1) apellidoMaterno = partes.slice(1).join(' ');

        const datos = {
            nombre,
            apellidoPaterno,
            apellidoMaterno,
            telefono,
            email: correo,
            fechaReserva,
            hora,
            idServicio
        };

        console.log("Enviando datos:", datos);

        //Cerrar modal de reserva
        const modalInstance = bootstrap.Modal.getInstance(reservasModal);
        if (modalInstance) {
            modalInstance.hide();
        }

        //Enviar datos al servidor
        fetch('../../backEnd/controladores/controladorAgregarReserva.php', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos)
        })
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(respuesta => {
            console.log("Respuesta del servidor:", respuesta);

            setTimeout(() => {
                if (respuesta.success) {
                    mostrarModalConfirmacion(
                        'success',
                        '¡Reserva Confirmada!',
                        respuesta.mensaje || 'Tu cita ha sido reservada exitosamente.',
                        {
                            fecha: formatearFecha(fechaReserva),
                            hora: formatearHora(hora),
                            servicio: obtenerNombreServicio(idServicio)
                        }
                    );
                    formReserva.reset();
                } else {
                    console.log(
                        'error',
                        'Error en la Reserva',
                        respuesta.mensaje || 'No se pudo completar la reserva. Por favor, intenta nuevamente.'
                    );
                }
            }, 400);
        })
        .catch(err => {
            console.error("Error al enviar la reserva:", err);
            
            setTimeout(() => {
                console.log(
                    'error',
                    'Error de Conexión',
                    'No se pudo comunicar con el servidor. Verifica tu conexión e intenta nuevamente.'
                );
            }, 400);
        });
    });

    const modalAdvertencia = document.getElementById('modalAdvertencia');
    modalAdvertencia.addEventListener('hidden.bs.modal', function () {
        const modalReserva = new bootstrap.Modal(reservasModal);
        modalReserva.show();
    });
});

/**
 * Valida que la hora esté entre 9:00 AM y 7:00 PM
 * @param {string} hora 
 * @returns {boolean}
 */
function validarHorario(hora) {
    const [horas, minutos] = hora.split(':').map(Number);
    const horaEnMinutos = horas * 60 + minutos;
    
    const horaInicio = 9 * 60;
    const horaFin = 19 * 60;

    return horaEnMinutos >= horaInicio && horaEnMinutos <= horaFin;
}

/**
 * Muestra el modal de advertencia de horario
 */
function mostrarModalAdvertencia() {
    const modal = document.getElementById('modalAdvertencia');
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
}

/**
 * Muestra el modal de confirmación
 * @param {string} tipo
 * @param {string} titulo
 * @param {string} mensaje
 * @param {object} detalles
 */
function mostrarModalConfirmacion(tipo, titulo, mensaje, detalles = null) {
    const modal = document.getElementById('modalConfirmacionReserva');
    const icono = document.getElementById('iconoConfirmacion');
    const tituloEl = document.getElementById('tituloConfirmacion');
    const mensajeEl = document.getElementById('mensajeConfirmacion');
    const detallesDiv = document.getElementById('detallesReserva');

    if (tipo === 'success') {
        icono.innerHTML = '<i class="fa-solid fa-circle-check text-success" style="font-size: 80px;"></i>';
    } else if (tipo === 'error') {
        icono.innerHTML = '<i class="fa-solid fa-circle-xmark text-danger" style="font-size: 80px;"></i>';
    }

    tituloEl.textContent = titulo;
    mensajeEl.textContent = mensaje;

    if (detalles && tipo === 'success') {
        document.getElementById('detalleFecha').textContent = detalles.fecha || '-';
        document.getElementById('detalleHora').textContent = detalles.hora || '-';
        document.getElementById('detalleServicio').textContent = detalles.servicio || '-';
        detallesDiv.classList.remove('d-none');
    } else {
        detallesDiv.classList.add('d-none');
    }

    // Mostrar el modal
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
}

/**
 * Formatea una fecha de YYYY-MM-DD a formato legible
 */
function formatearFecha(fecha) {
    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    const [año, mes, dia] = fecha.split('-');
    return `${parseInt(dia)} de ${meses[parseInt(mes) - 1]} de ${año}`;
}

/**
 * Formatea la hora de 24h a 12h con AM/PM
 */
function formatearHora(hora) {
    const [horas, minutos] = hora.split(':');
    let h = parseInt(horas);
    const periodo = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return `${h}:${minutos} ${periodo}`;
}

/**
 * Obtiene el nombre del servicio desde el select
 */
function obtenerNombreServicio(idServicio) {
    const selectServicio = document.getElementById('selectServicioModal');
    const opcion = selectServicio.querySelector(`option[value="${idServicio}"]`);
    return opcion ? opcion.textContent : 'Servicio seleccionado';
}
