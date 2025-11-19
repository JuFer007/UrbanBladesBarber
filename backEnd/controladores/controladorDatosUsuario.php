<?php
require_once(__DIR__ . '/../../backEnd/dao/DAO_UsuarioEmpleado.php');

header('Content-Type: application/json');

$nombreUsuario = $_POST['usuario'] ?? null;

if (!$nombreUsuario) {
    echo json_encode([
        "error"   => true,
        "usuario" => null,
        "cargo"   => null,
        "genero"  => null
    ]);
    exit;
}

try {
    $daoUsuario = new DAO_UsuarioEmpleado();
    $resultado = $daoUsuario->obtenerNombreCompletoCargoPorUsuario($nombreUsuario);

    if ($resultado) {
        echo json_encode([
            "error"   => false,
            "usuario" => $resultado['nombreCompleto'],
            "cargo"   => $resultado['cargo'],
            "genero"  => $resultado['genero']
        ]);
    } else {
        echo json_encode([
            "error"   => false,
            "usuario" => null,
            "cargo"   => null,
            "genero"  => null
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "error"   => true,
        "usuario" => null,
        "cargo"   => null,
        "genero"  => null
    ]);
}
