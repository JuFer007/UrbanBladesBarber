<?php
require_once(__DIR__ . '/../dao/DAO_Empleado.php');

if (isset($_GET['dni'])) {
    $dni = $_GET['dni'];
    $daoEmpleado = new DAO_Empleado();
    $perfil = $daoEmpleado->obtenerPerfilPorDNI($dni);

    header('Content-Type: application/json');
    echo json_encode($perfil);
}
?>
