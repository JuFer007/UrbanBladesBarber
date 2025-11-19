<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../dao/DAO_Empleado.php');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        throw new Exception("No se recibieron datos");
    }

    $nombre = $data['nombre'] ?? '';
    $apellidoPaterno = $data['apellidoPaterno'] ?? '';
    $apellidoMaterno = $data['apellidoMaterno'] ?? '';
    $telefono = $data['telefono'] ?? '';
    $salario = $data['salario'] ?? 0;
    $dni = $data['dni'] ?? '';

    $daoEmpleado = new DAO_Empleado();
    $resultado = $daoEmpleado->actualizarEmpleado($nombre, $apellidoPaterno, $apellidoMaterno, $telefono, $salario, $dni);

    if ($resultado) {
        echo json_encode([
            'success' => true,
            'mensaje' => 'Empleado actualizado correctamente.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'mensaje' => 'No se pudo actualizar el empleado.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error: ' . $e->getMessage()
    ]);
}
?>
