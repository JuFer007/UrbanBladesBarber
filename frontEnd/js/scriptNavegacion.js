document.addEventListener('DOMContentLoaded', function() {
    const enlacesSidebar = document.querySelectorAll('.side-menu a');
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
            elemento.style.display = 'block';
        }
    }

    function actualizarEnlaceActivo(indiceActivo) {
        enlacesSidebar.forEach((enlace, indice) => {
            if (indice === indiceActivo) {
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

            let indiceActivo = -1;
            enlacesSidebar.forEach((enlace, i) => {
                if (enlace.dataset.seccion === nombreSeccion) {
                    indiceActivo = i;
                }
            });
            actualizarEnlaceActivo(indiceActivo);
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

            switch (cargoUsuario) {
                case 'Barbero':
                    seccionesArray = ['divHorarioReserva'];
                    break;
                case 'Recepcionista':
                    seccionesArray = ['divClientes', 'divReservas', 'divPagos'];
                    break;
                case 'Administrador':
                    seccionesArray = ['divDashboard', 'divEmpleados', 'divServicios', 'divClientes', 'divReservas', 'divPagos'];
                    break;
                default:
                    console.warn('Cargo de usuario desconocido:', cargoUsuario);
            }

            const seccionesPermitidas = permisos[cargoUsuario];

            if (seccionesPermitidas) {
                enlacesSidebar.forEach(enlace => {
                    const seccionObjetivo = enlace.dataset.seccion;
                    if (!seccionesPermitidas.includes(seccionObjetivo)) {
                        enlace.parentElement.style.display = 'none';
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
            cambiarSeccion(seccionesArray[0]);
        }
    }

    const cambiarSeccionOriginal = cambiarSeccion;
    cambiarSeccion = function(nombreSeccion) {
        cambiarSeccionOriginal(nombreSeccion);
        guardarEstado(nombreSeccion);
    };

    ajustarVisibilidadPorCargo();
    restaurarEstado();
    
    window.debugSidebar = function() {
        console.log('Secciones disponibles:', seccionesArray);
        console.log('Sección actual:', obtenerSeccionActual());
        console.log('Enlaces encontrados:', enlacesSidebar.length);
    };
});
