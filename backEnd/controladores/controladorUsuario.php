<?php
require_once(__DIR__ . '/../../backEnd/dao/DAO_UsuarioEmpleado.php');

header('Content-Type: application/json');

$nombreUsuario = $_POST['usuario'] ?? null;
$contrasenia   = $_POST['contrasena'] ?? null;

if (!$nombreUsuario || !$contrasenia) {
    echo json_encode([
        "error" => true,
        "valido" => false,
        "estado" => null,
        "usuario" => null,
        "cargo" => null
    ]);
    exit;
}

try {
    $daoUsuario = new DAO_UsuarioEmpleado();
    $resultado = $daoUsuario->verificarUsuario($nombreUsuario, $contrasenia);

    if ($resultado['valido']) {
        echo json_encode([
            "error"   => false,
            "valido"  => true,
            "estado"  => $resultado['estado'],
            "usuario" => $resultado['usuario'],
            "cargo"   => $resultado['cargo']
        ]);
    } else {
        echo json_encode([
            "error"   => false,
            "valido"  => false,
            "estado"  => null,
            "usuario" => null,
            "cargo"   => null
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "error" => true,
        "valido" => false,
        "estado" => null,
        "usuario" => null,
        "cargo"   => null
    ]);
}
?>