<?php
require_once(__DIR__ . '/../../backEnd/dao/DAO_Reporte.php');

header('Content-Type: application/json');

$daoReporte = new DAO_Reporte();
$datosGrafico2 = $daoReporte->ingresosPorMesyMetodo();

echo json_encode($datosGrafico2);
?>
