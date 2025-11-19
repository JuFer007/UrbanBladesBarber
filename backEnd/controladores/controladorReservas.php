<?php
header('Content-Type: application/json');

try {
    require_once(__DIR__ . '/../dao/DAO_Reserva.php');
    require_once(__DIR__ . '/../dao/DAO_Barbero.php');

    // Obtener los datos enviados (por JSON o GET)
    $input = json_decode(file_get_contents('php://input'), true);
    $accion = $input['accion'] ?? $_GET['accion'] ?? null;

    if (!$accion) {
        throw new Exception('No se especificó ninguna acción.');
    }

    $data = [];

    switch ($accion) {
        case 'listarReservasPorFecha':
            $fecha = $input['fecha'] ?? $_GET['fecha'] ?? null;
            
            if (!$fecha) {
                throw new Exception('No se especificó una fecha.');
            }

            $daoReserva = new DAO_Reserva();
            $data = $daoReserva->listarReservasPorFecha($fecha);
            
            break;

        case 'listarBarberos':
            $daoBarbero = new DAO_Barbero();
            $data = $daoBarbero->listarBarberos();            
            break;

        default:
            throw new Exception('Acción no reconocida: ' . $accion);
    }

    echo json_encode([
        'success' => true,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("ERROR: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
