document.addEventListener("DOMContentLoaded", () => {
    const serviciosContainer = document.getElementById("servicios-container");
    const modalSelectServicio = document.getElementById("selectServicioModal");
    const reservasModal = document.getElementById('reservasModal');
    if (serviciosContainer) {
        fetch('../../../backEnd/controladores/controladorServicios.php')
            .then(response => response.text())
            .then(html => {
                serviciosContainer.innerHTML = html;
            })
            .catch(err => {
                console.error('Error al cargar las tarjetas de servicios:', err);
                serviciosContainer.innerHTML = "<p class='text-center text-danger'>No se pudieron cargar los servicios. Intente más tarde.</p>";
            });
    }

    // Carga las opciones de servicios en el <select> del modal
    if (modalSelectServicio) {
        fetch('../../../backEnd/controladores/controladorServicios.php?formato=json')
            .then(response => response.json())
            .then(servicios => {
                servicios.forEach(servicio => {
                    const option = document.createElement('option');
                    option.value = servicio.id;
                    option.textContent = servicio.nombre;
                    modalSelectServicio.appendChild(option);
                });
            })
            .catch(err => {
                console.error('Error al cargar opciones de servicios:', err);
                const option = document.createElement('option');
                option.textContent = 'Error al cargar servicios';
                modalSelectServicio.appendChild(option);
            });
    }

    // Evento para pre-seleccionar el servicio cuando se abre el modal
    if (reservasModal) {
        reservasModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const servicioId = button.getAttribute('data-servicio-id');

            modalSelectServicio.value = servicioId;
        });
    }
});
