<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);

try {
    require_once(__DIR__ . '/../dao/DAO_Reserva.php');
    require_once(__DIR__ . '/../modelos/Reserva.php');

    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) throw new Exception("No se recibieron datos.");

    $idCliente = $input['idCliente'] ?? null;
    $idServicio = $input['idServicio'] ?? null;
    $fechaReserva = $input['fechaReserva'] ?? null;
    $hora = $input['hora'] ?? null;
    $estado = "Confirmada";

    if (!$idCliente || !$idServicio || !$fechaReserva || !$hora) {
        throw new Exception("Faltan datos obligatorios para la reserva.");
    }

    //Crear la reserva usando el constructor completo
    $reserva = new Reserva(
        null,
        $idCliente,
        null,
        $idServicio,
        $fechaReserva,
        $hora,
        $estado
    );

    $daoReserva = new DAO_Reserva();
    $resultado = $daoReserva->agregarNuevaReserva($reserva);

    if (!$resultado) {
        throw new Exception("No se pudo registrar la reserva. Verifique disponibilidad.");
    }

    echo json_encode([
        'success' => true,
        'mensaje' => 'Reserva registrada correctamente.'
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    error_log("ERROR: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
