<tbody id="empleados-body">
<?php
require_once(__DIR__ . '/../../backEnd/dao/DAO_Empleado.php');

//Cargar datos para tabla de empleados
$daoEmpleado = new DAO_Empleado();
$empleados = $daoEmpleado->listarEmpleados();
$contador = 1;

foreach ($empleados as $emp) {
    $dni = $emp->getDNIempleado();

    echo "<tr>";
    echo "<td class='dni-empleado' data-dni='{$dni}'>{$emp->getDNIempleado()}</td>";
    echo "<td>{$emp->getNombreE()}</td>";    
    echo "<td>{$emp->getApellidoPaternoE()}</td>";
    echo "<td>{$emp->getApellidoMaternoE()}</td>";
    echo "<td>{$emp->getTelefono()}</td>";
    echo "<td>{$emp->getSalario()}</td>";
    echo "<td>{$emp->getCargo()}</td>";

    $estado = $emp->getEstadoEmpleado();
    $estadoEmpleado = (strtolower($estado) === "activo") ? "estado-activo" : "estado-inactivo";
    echo "<td><span class='{$estadoEmpleado}'>{$estado}</span></td>";

    echo "<td>{$emp->getGenero()}</td>";
    echo "</tr>";
    $contador++;
}
?>
</tbody>
