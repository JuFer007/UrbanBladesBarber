<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Función helper para logs
function logMsg($msg) {
    error_log("[" . date('Y-m-d H:i:s') . "] " . $msg);
}

try {
    require_once(__DIR__ . '/../dao/DAO_Reserva.php');
    require_once(__DIR__ . '/../dao/DAO_Barbero.php');
    require_once(__DIR__ . '/../modelos/Reserva.php');

    // --- Leer entrada JSON ---
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception("No se recibieron datos JSON válidos.");
    }

    //Validar campos obligatorios
    $idReserva = $input['idReserva'] ?? null;
    $nuevaFecha = $input['fechaReserva'] ?? null;
    $nuevaHora = $input['hora'] ?? null;

    if (!$idReserva || !$nuevaFecha || !$nuevaHora) {
        throw new Exception("Faltan datos obligatorios (idReserva, fechaReserva, hora).");
    }

    //Validar formato de fecha (YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $nuevaFecha)) {
        throw new Exception("Formato de fecha inválido. Use YYYY-MM-DD.");
    }

    //Validar formato de hora (HH:MM)
    if (!preg_match('/^\d{2}:\d{2}$/', $nuevaHora)) {
        throw new Exception("Formato de hora inválido. Use HH:MM.");
    }

    if (strlen($nuevaHora) === 5) {
        $nuevaHora .= ':00';
    }

    // --- Procesar edición ---
    $daoReserva = new DAO_Reserva();

    $resultado = $daoReserva->editarReserva($idReserva, $nuevaFecha, $nuevaHora);
    
    if (!$resultado) {
        throw new Exception("No se pudo actualizar la reserva. No hay disponibilidad para esa fecha y hora.");
    }

    echo json_encode([
        'success' => true,
        'mensaje' => 'Reserva actualizada correctamente.'
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
