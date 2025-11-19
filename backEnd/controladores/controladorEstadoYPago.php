<?php
    header('Content-Type: application/json; charset=utf-8');
    ini_set('display_errors', 0);
    error_reporting(E_ALL);

    set_exception_handler(function($e) {
        echo json_encode(['success' => false, 'mensaje' => "Excepción no controlada: " . $e->getMessage()]);
        exit;
    });
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        echo json_encode(['success' => false, 'mensaje' => "Error PHP: $errstr en $errfile:$errline"]);
        exit;
    });

    require_once __DIR__ . '/../dao/DAO_Reserva.php';
    require_once __DIR__ . '/../dao/DAO_Pago.php';
    require_once __DIR__ . '/../dao/DAO_Servicio.php';
    require_once __DIR__ . '/../modelos/Pago.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $idReserva      = $data['idReserva'] ?? null;
    $nombreServicio = $data['nombreServicio'] ?? null;
    $metodo         = $data['metodo'] ?? null;
    $fechaPago      = date("Y-m-d H:i:s");

    try {
        if (!$idReserva || !$nombreServicio) {
            throw new Exception("Faltan datos obligatorios: idReserva o nombreServicio");
        }

        $daoReserva = new DAO_Reserva();

        // Guardar estado anterior para posible rollback manual
        // (usamos buscarPorId si lo tienes; si no, asumimos 'Pendiente' previo)
        $reservaAntes = null;
        if (method_exists($daoReserva, 'buscarPorIdReserva')) {
            // si implementaste un método que devuelve la reserva completa
            $reservaAntes = $daoReserva->buscarPorIdReserva($idReserva);
        }

        $resultadoEstado = $daoReserva->cambiarEstado($idReserva);
        if (!$resultadoEstado) {
            throw new Exception("No se pudo cambiar el estado de la reserva");
        }

        $daoServicio = new DAO_Servicio();
        $montoPago = $daoServicio->obtenerMontoServicio($nombreServicio);
        if ($montoPago === null) {
            // revertir estado de reserva si falla buscar precio
            // intentar revertir a estado anterior si lo conocemos
            if ($reservaAntes && isset($reservaAntes['estado'])) {
                $daoReserva->revertirEstado($idReserva, $reservaAntes['estado']);
            }
            throw new Exception("No se encontró el precio del servicio $nombreServicio");
        }

        // crear objeto Pago con estado null (DAO lo guardará como "Pendiente")
        $pago = new Pago(null, $idReserva, $montoPago, $metodo ?? '', $fechaPago, 'Pendiente');
        $daoPago = new DAO_Pago();

        try {
            $resultadoPago = $daoPago->agregarNuevoPago($pago);
        } catch (Exception $e) {
            // intentar revertir la reserva (ponerla como estaba)
            if ($reservaAntes && isset($reservaAntes['estado'])) {
                $daoReserva->revertirEstado($idReserva, $reservaAntes['estado']);
            }
            throw new Exception("Error al registrar orden de pago: " . $e->getMessage());
        }

        echo json_encode([
            'success' => true,
            'mensaje' => 'Reserva completada y orden de pago registrada (pendiente)',
            'monto'   => $montoPago,
            'servicio'=> $nombreServicio
        ]);
    } catch (Exception $ex) {
        echo json_encode(['success' => false, 'mensaje' => $ex->getMessage()]);
    }
?>
