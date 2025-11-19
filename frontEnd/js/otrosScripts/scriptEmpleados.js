window.addEventListener('DOMContentLoaded', () => {
  fetch('../../backEnd/controladores/controladorEmpleado.php')
    .then(response => response.text())
    .then(html => {
      document.getElementById('empleados-body').innerHTML = html;
    })
    .catch(err => console.error('Error cargando empleados:', err));
});
