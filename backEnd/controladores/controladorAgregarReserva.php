<?php
ob_start(); 
error_reporting(E_ERROR); // solo errores fatales
header('Content-Type: application/json');

try {
    require_once(__DIR__ . '/../dao/DAO_Cliente.php');
    require_once(__DIR__ . '/../modelos/Cliente.php');
    require_once(__DIR__ . '/../dao/DAO_Reserva.php');
    require_once(__DIR__ . '/../modelos/Reserva.php');

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) throw new Exception("No se recibieron datos");

    $nombre = trim($data['nombre'] ?? '');
    $apellidoP = trim($data['apellidoPaterno'] ?? '');
    $apellidoM = trim($data['apellidoMaterno'] ?? '');
    $telefono = trim($data['telefono'] ?? '');
    $email = trim($data['email'] ?? '');
    $idServicio = $data['idServicio'] ?? null;
    $fechaReserva = $data['fechaReserva'] ?? null;
    $hora = $data['hora'] ?? null;

    if (!$nombre || !$apellidoP || !$apellidoM || !$idServicio || !$fechaReserva || !$hora) {
        throw new Exception("Faltan datos obligatorios");
    }

    $daoCliente = new DAO_Cliente();
    $idCliente = $daoCliente->verificarCliente($nombre, $apellidoP, $apellidoM);

    if (!$idCliente) {
        //Crear nuevo cliente
        $cliente = new Cliente(null, $nombre, $apellidoP, $apellidoM, $telefono, $email);
        $daoCliente->agregarNuevoCliente($cliente);

        //Obtener el último ID insertado
        $idCliente = $daoCliente->obtenerUltimoIDCliente();

        if (!$idCliente) throw new Exception("No se pudo obtener el ID del nuevo cliente");
    }

    //Crear reserva con el ID del nuevo cliente
    $reserva = new Reserva(null, $idCliente, null, $idServicio, $fechaReserva, $hora, "Confirmada");
    $daoReserva = new DAO_Reserva();
    $resultado = $daoReserva->agregarNuevaReserva($reserva);

    ob_end_clean();
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'mensaje' => 'Reserva registrada correctamente.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'mensaje' => 'No hay barberos disponibles en ese horario. Por favor, elige otra hora o fecha.'
        ]);
    }

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ]);
}
