fetch('../../backEnd/controladores/controladorReportes.php')
  .then(res => res.json())
  .then(data => {
    console.log(data);
    document.getElementById("cantidadEmpleados").textContent = data.numEmpleados;
    document.getElementById("cantidadReservas").textContent = data.cantidadDeReservas;
    document.getElementById("ingresosTotales").textContent = "S/. " + data.totalGanancias;
    document.getElementById("numeroClientes").textContent = data.numClientes;
  })
  .catch(err => console.error("Error cargando datos:", err));
  