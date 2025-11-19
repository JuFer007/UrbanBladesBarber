<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);

try {
    require_once(__DIR__ . '/../dao/DAO_Reserva.php');

    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) throw new Exception("No se recibieron datos.");

    $idReserva = $input['idReserva'] ?? null;

    if (!$idReserva) {
        throw new Exception("El ID de la reserva es obligatorio para cancelarla.");
    }

    $daoReserva = new DAO_Reserva();
    $resultado = $daoReserva->cancelarReserva($idReserva);
    
    if (!$resultado) {
        throw new Exception("No se pudo cancelar la reserva. Verifique el ID proporcionado.");
    }

    echo json_encode([
        'success' => true,
        'mensaje' => 'Reserva cancelada correctamente.'
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
