<?php
require_once(__DIR__ . '/../../backEnd/dao/DAO_UsuarioEmpleado.php');
header('Content-Type: application/json');

$nombreUsuario = $_POST['usuario'] ?? null;

if (!$nombreUsuario) {
    echo json_encode([
        "error" => true,
        "idBarbero" => null
    ]);
    exit;
}

try {
    $daoUsuario = new DAO_UsuarioEmpleado();

    $idBarbero = $daoUsuario->obtenerIdEmpleadoPorUsuario($nombreUsuario);

    if ($idBarbero) {
        echo json_encode([
            "error" => false,
            "idBarbero" => $idBarbero
        ]);
    } else {
        echo json_encode([
            "error" => true,
            "idBarbero" => null
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "error" => true,
        "idBarbero" => null,
        "mensaje" => $e->getMessage()
    ]);
}
?>
