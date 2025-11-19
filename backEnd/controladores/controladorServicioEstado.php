<?php
header('Content-Type: application/json');

try {
    require_once(__DIR__ . '/../dao/DAO_Servicio.php');

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['idServicio']) || !isset($input['accion'])) {
        throw new Exception('Datos incompletos. Se requiere idServicio y accion.');
    }

    $idServicio = intval($input['idServicio']);
    $accion = $input['accion'];

    if ($idServicio <= 0) {
        throw new Exception('ID de servicio no válido.');
    }

    $daoServicio = new DAO_Servicio();
    $resultado = false;

    if ($accion === 'habilitar') {
        $resultado = $daoServicio->habilitarServicio($idServicio);
    } elseif ($accion === 'deshabilitar') {
        $resultado = $daoServicio->deshabilitarServicio($idServicio);
    }

    if ($resultado) {
        echo json_encode(['success' => true, 'message' => 'Estado del servicio actualizado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el estado del servicio.']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>