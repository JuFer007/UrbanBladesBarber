document.addEventListener("DOMContentLoaded", () => {
  //para recargar la tabla
  function recargarTablaEmpleados() {
    fetch('../../backEnd/controladores/controladorEmpleado.php')
      .then(response => response.text())
      .then(html => {
        document.getElementById('empleados-body').innerHTML = html;
      })
      .catch(err => console.error('Error recargando empleados:', err));
  }  

  const tbody = document.getElementById("empleados-body");
  const sistemaToast = new SistemaToast();

  const modalConfirm = document.getElementById("modalConfirm");
  const btnConfirmar = document.getElementById("btnConfirmar");
  const btnCancelar = document.getElementById("btnCancelarModal");

  let payloadGuardar = null;
  let modoEdicion = false;

  tbody.addEventListener("click", (e) => {
    const fila = e.target.closest("tr");
    if (!fila) return;

    const dniCell = fila.querySelector(".dni-empleado");
    if (!dniCell) return;

    const dni = dniCell.dataset.dni;
    document.body.dataset.dniSeleccionado = dni;

    fetch(`/backEnd/controladores/controladorPerfilEmpleado.php?dni=${dni}`)
      .then(res => res.json())
      .then(data => {
        let fotoSrc = data.generoE === "Femenino" ? "/recursos/userMujer.png" : "/recursos/userHombre.png";

        const perfilHTML = `
          <div class="perfil-datos">
            <h3 id="nombreCompleto">
              <span id="nombreEmpleado">${data.nombreEmpleado}</span> 
              <span id="apellidoPaternoE">${data.apellidoPaternoE}</span> 
              <span id="apellidoMaternoE">${data.apellidoMaternoE}</span>
            </h3>
            <p><span class="cargo-empleado">${data.cargo}</span></p>
            <ul>
              <li><b>DNI</b><span>${data.dni}</span></li>
              <li><b>Teléfono</b><span id="telefonoEmpleado">${data.telefono}</span></li>
              <li><b>Cargo</b><span>${data.cargo}</span></li>
              <li><b>Salario</b><span id="salarioEmpleado">${data.salario}</span></li>
            </ul>
          </div>
        `;

        document.querySelector(".perfil-empleado .perfil-datos").innerHTML = perfilHTML;
        document.getElementById("fotoEmpleado").src = fotoSrc;
        document.getElementById("perfilEmpleado").style.display = "flex";

        const btnEditar = document.getElementById("btnEditar");

        btnEditar.onclick = () => {
          if (!modoEdicion) {
            const campos = [
              { id: "nombreEmpleado", type: "text" },
              { id: "apellidoPaternoE", type: "text" },
              { id: "apellidoMaternoE", type: "text" },
              { id: "telefonoEmpleado", type: "text" },
              { id: "salarioEmpleado", type: "number" }
            ];

            campos.forEach(campo => {
              const span = document.getElementById(campo.id);
              const valorActual = span.textContent.trim();
              span.innerHTML = `<input type="${campo.type}" id="${campo.id}Input" value="${valorActual}">`;
            });

            btnEditar.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar';
            modoEdicion = true;
          } else {
            payloadGuardar = {
              dni: data.dni,
              nombre: document.getElementById("nombreEmpleadoInput").value,
              apellidoPaterno: document.getElementById("apellidoPaternoEInput").value,
              apellidoMaterno: document.getElementById("apellidoMaternoEInput").value,
              telefono: document.getElementById("telefonoEmpleadoInput").value,
              salario: document.getElementById("salarioEmpleadoInput").value
            };

            modalConfirm.classList.add("show");
          }
        };
      })
      .catch(err => sistemaToast.mostrar("error", "Error cargando perfil: " + err));
  });

  btnConfirmar.addEventListener("click", () => {
    if (!payloadGuardar) return;

    fetch("/backEnd/controladores/controladorActualizarEmpleado.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payloadGuardar)
    })
    .then(res => res.json())
    .then(resp => {
      if (resp.success) {
        sistemaToast.mostrar("success", "Cambios guardados correctamente");

        recargarTablaEmpleados();

        // Restaurar valores en spans
        document.getElementById("nombreEmpleado").textContent = payloadGuardar.nombre;
        document.getElementById("apellidoPaternoE").textContent = payloadGuardar.apellidoPaterno;
        document.getElementById("apellidoMaternoE").textContent = payloadGuardar.apellidoMaterno;
        document.getElementById("telefonoEmpleado").textContent = payloadGuardar.telefono;
        document.getElementById("salarioEmpleado").textContent = payloadGuardar.salario;

        document.getElementById("btnEditar").innerHTML = '<i class="fa-solid fa-pencil"></i> Editar';
        modoEdicion = false;
      } else {
        sistemaToast.mostrar("error", "Error al guardar los cambios");
      }
      modalConfirm.classList.remove("show");
      payloadGuardar = null;
    })
    .catch(err => sistemaToast.mostrar("error", "Error en fetch: " + err));
  });

  btnCancelar.addEventListener("click", () => {
    modalConfirm.classList.remove("show");
    payloadGuardar = null;

    if (modoEdicion) {
      const campos = ["nombreEmpleado", "apellidoPaternoE", "apellidoMaternoE", "telefonoEmpleado", "salarioEmpleado"];
      campos.forEach(id => {
        const input = document.getElementById(id + "Input");
        if (input) {
          document.getElementById(id).textContent = input.value;
        }
      });
      document.getElementById("btnEditar").innerHTML = '<i class="fa-solid fa-pencil"></i> Editar';
      modoEdicion = false;
    }
  });
});
