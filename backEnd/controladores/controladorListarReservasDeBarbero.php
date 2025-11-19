<?php
require_once(__DIR__ . '/../../backEnd/dao/DAO_Reserva.php');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$idBarbero = $_POST['idBarbero'] ?? null;

if (!$idBarbero) {
    echo json_encode([
        "error" => true,
        "reservas" => [],
        "mensaje" => "No se proporcionó el ID del barbero"
    ]);
    exit;
}

try {
    $daoReserva = new DAO_Reserva();
    $reservas = $daoReserva->listarReservasID($idBarbero);

    if ($reservas) {
        echo json_encode([
            "error" => false,
            "reservas" => $reservas
        ]);
    } else {
        echo json_encode([
            "error" => false,
            "reservas" => [],
            "mensaje" => "No se encontraron reservas para este barbero"
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "error" => true,
        "reservas" => [],
        "mensaje" => $e->getMessage()
    ]);
}
