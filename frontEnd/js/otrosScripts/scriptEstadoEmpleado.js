document.addEventListener("DOMContentLoaded", () => {
  const sistemaToast = new SistemaToast();
  const btnHabilitar = document.getElementById("btnHabilitar");
  const btnDeshabilitar = document.getElementById("btnDeshabilitar");

  //para recargar la tabla
  function recargarTablaEmpleados() {
    fetch('../../backEnd/controladores/controladorEmpleado.php')
      .then(response => response.text())
      .then(html => {
        document.getElementById('empleados-body').innerHTML = html;
      })
      .catch(err => console.error('Error recargando empleados:', err));
  }  

  //funcion para cambiar el estado
  function cambiarEstadoEmpleado(dni, accion) {
    fetch("/backEnd/controladores/controladorEstadoEmpleado.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ dni: dni, accion: accion })
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          sistemaToast.mostrar("info", (accion === "habilitar") ? "Empleado habilitado correctamente." : "Empleado deshabilitado correctamente.");

          recargarTablaEmpleados();

          const estadoCell = document.querySelector(
            `tr .dni-empleado[data-dni="${dni}"]`
          )?.closest("tr")?.querySelector(".estado-empleado");

          if (estadoCell) {
            estadoCell.textContent = (accion === "habilitar") ? "Activo" : "Inactivo";
          }
        } else {
          sistemaToast.mostrar("error", "No se pudo actualizar el estado del empleado.");
        }
      })
      .catch(err => alert("Error en fetch: " + err));
  }

  //boton para deshabilitar
  btnDeshabilitar.addEventListener("click", () => {
    const dni = document.body.dataset.dniSeleccionado;
    if (!dni) {
      sistemaToast.mostrar("warning", "Debe seleccionar un empleado de la tabla antes de deshabilitar.");
      return;
    }

    const estado = document.querySelector(`.dni-empleado[data-dni="${dni}"]`)
      ?.closest("tr")
      ?.querySelector(".estado-empleado")?.textContent.trim();

    if (estado === "Inactivo") {
      sistemaToast.mostrar("info", "El empleado ya está inactivo.");
      return;
    }
    cambiarEstadoEmpleado(dni, "deshabilitar");
  });

  //boton para habilitar
  btnHabilitar.addEventListener("click", () => {
    const dni = document.body.dataset.dniSeleccionado;
    if (!dni) {
      sistemaToast.mostrar("warning", "Debe seleccionar un empleado de la tabla antes de habilitar.");
      return;
    }

    const estado = document.querySelector(`.dni-empleado[data-dni="${dni}"]`)
      ?.closest("tr")
      ?.querySelector(".estado-empleado")?.textContent.trim();

    if (estado === "Activo") {
      sistemaToast.mostrar("info", "El empleado ya está activo.");
      return;
    }
    cambiarEstadoEmpleado(dni, "habilitar");
  });
});
