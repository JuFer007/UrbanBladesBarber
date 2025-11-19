<?php
require_once(__DIR__ . '/../../backEnd/dao/DAO_Reporte.php');

//Cargar datos para los reportes
$daoReporte = new DAO_Reporte;

header('Content-Type: application/json');

//Cargar datos por tablas
echo json_encode([
    "numEmpleados" => $daoReporte->totalEmpleados(),
    "cantidadDeReservas" => $daoReporte->cantidadReservas(),
    "totalGanancias" => $daoReporte->totalGanancias(),
    "numClientes" => $daoReporte->totalClientes()
]);

?>
