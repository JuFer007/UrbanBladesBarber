<tbody id="servicios-body">
<?php
require_once(__DIR__ . '/../../backEnd/dao/DAO_Servicio.php');

//CARGAR DATOS PARA LA TABLA DE SERVICIOS
$daoServicio = new DAO_Servicio();
$servicios = $daoServicio ->listarServicios();
$contador = 1;

foreach ($servicios as $serv){
    $idServicio = $serv->getIdServicio();
    echo "<tr data-id='{$idServicio}'>";
    echo "<td>{$contador}</td>";
    echo "<td>{$serv->getNombreServicio()}</td>";
    echo "<td>S/ " . number_format($serv->getPrecio(), 2) . "</td>";
    echo "<td>{$serv->getDescripcion()}</td>";

    $estado = $serv->getEstadoS();
    $estadoS = (strtolower($estado) === "activo") ? "estado-activo" : "estado-inactivo";
    echo "<td><span class='{$estadoS}'>{$estado}</span></td>";
    echo "</tr>";
    $contador++;
}
?>
</tbody>