document.addEventListener('DOMContentLoaded', function() {
    // Se seleccionan todos los enlaces de navegación que tienen el atributo data-seccion.
    const enlacesSidebar = document.querySelectorAll('.side-menu a[data-seccion]');
    const todasLasSecciones = [
        'divDashboard',
        'divEmpleados',
        'divServicios',
        'divClientes',
        'divHorarioReserva',
        'divReservas',
        'divPagos'
    ];
    let seccionesArray = [...todasLasSecciones];

    function ocultarTodasLasSecciones() {
        todasLasSecciones.forEach(id => {
            const elemento = document.getElementById(id);
            if (elemento) {
                elemento.style.display = 'none';
            }
        });
    }

    function mostrarSeccion(idSeccion) {
        const elemento = document.getElementById(idSeccion);
        if (elemento) {
            // Cambiamos a 'block' para que sea compatible con el MutationObserver de otras secciones (como servicios)
            elemento.style.display = 'block';
        }
    }

    function actualizarEnlaceActivo(enlaceActivo) {
        enlacesSidebar.forEach(enlace => {
            if (enlace === enlaceActivo) {
                enlace.classList.add('active');
            } else {
                enlace.classList.remove('active');
            }
        });
    }

    function cambiarSeccion(nombreSeccion) {
        if (seccionesArray.includes(nombreSeccion)) {
            ocultarTodasLasSecciones();
            mostrarSeccion(nombreSeccion);

            let enlaceActivo = null;
            enlacesSidebar.forEach(enlace => {
                if (enlace.dataset.seccion === nombreSeccion) {
                    enlaceActivo = enlace;
                }
            });
            actualizarEnlaceActivo(enlaceActivo);

            guardarEstado(nombreSeccion);
        }
    }

    function ajustarVisibilidadPorCargo() {
        const cargoUsuario = localStorage.getItem('usuarioCargo');
        if (cargoUsuario) {
            const permisos = {
                'Barbero': ['divHorarioReserva'],
                'Recepcionista': ['divClientes', 'divReservas', 'divPagos'],
                'Administrador' : ['divDashboard', 'divEmpleados', 'divServicios', 'divClientes', 'divReservas', 'divPagos']
            };

            // Actualiza las secciones permitidas para el rol
            if (permisos[cargoUsuario]) {
                seccionesArray = permisos[cargoUsuario];
            } else {
                console.warn('Cargo de usuario desconocido:', cargoUsuario);
                seccionesArray = [];
            }

            const seccionesPermitidas = permisos[cargoUsuario];

            if (seccionesPermitidas) {
                enlacesSidebar.forEach(enlace => {
                    const seccionObjetivo = enlace.dataset.seccion;
                    if (!seccionesPermitidas.includes(seccionObjetivo)) {
                        // Ocultamos el enlace directamente
                        enlace.style.display = 'none';
                    }
                });
            }
        }
    }

    enlacesSidebar.forEach(enlace => {
        enlace.addEventListener('click', function(e) {
            e.preventDefault();
            const nombreSeccion = this.dataset.seccion;
            cambiarSeccion(nombreSeccion);
        });
    });

    window.navegarA = function(nombreSeccion) {
        if (nombreSeccion) {
            cambiarSeccion(nombreSeccion);
        }
    };
    
    window.obtenerSeccionActual = function() {
        for (let i = 0; i < seccionesArray.length; i++) {
            const elemento = document.getElementById(seccionesArray[i]);
            if (elemento && elemento.style.display !== 'none') {
                return seccionesArray[i];
            }
        }
        return null;
    };
    
    function guardarEstado(nombreSeccion) {
        localStorage.setItem('seccionActiva', nombreSeccion);
    }
    
    function restaurarEstado() {
        const estadoGuardado = localStorage.getItem('seccionActiva');
        if (estadoGuardado && seccionesArray.includes(estadoGuardado)) {
            cambiarSeccion(estadoGuardado);
        } else {
            cambiarSeccion(seccionesArray[0] || 'divDashboard'); // Fallback a dashboard
        }
    }

    ajustarVisibilidadPorCargo();
    restaurarEstado();
});
