document.addEventListener('DOMContentLoaded', () => {
    const inputBuscar = document.getElementById('buscarEmpleado');
    const tbody = document.getElementById('empleados-body');

    inputBuscar.addEventListener('input', () => {
    const filtro = inputBuscar.value.toLowerCase();
    const filas = tbody.getElementsByTagName('tr');

    for (let i = 0; i < filas.length; i++) {
        const fila = filas[i];
        const celdas = fila.getElementsByTagName('td');
        let encontrado = false;

        for (let j = 0; j < celdas.length; j++) {
            const valorCelda = celdas[j].textContent.toLowerCase();
            if (valorCelda.includes(filtro)) {
                encontrado = true;
                break;
            }
        }

        fila.style.display = encontrado ? '' : 'none';
    }
    });
});