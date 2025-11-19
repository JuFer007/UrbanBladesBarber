<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

set_exception_handler(function($e) {
    echo json_encode([
        'success' => false,
        'mensaje' => "Excepción no controlada: " . $e->getMessage()
    ]);
    exit;
});

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo json_encode([
        'success' => false,
        'mensaje' => "Error PHP: $errstr en $errfile:$errline"
    ]);
    exit;
});

require_once __DIR__ . '/../dao/DAO_Cliente.php';
require_once __DIR__ . '/../modelos/Cliente.php';

$daoCliente = new DAO_Cliente();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'PUT') {
    //Actualizar cliente
    $data = json_decode(file_get_contents("php://input"), true);

    $nombre = $data['nombre'] ?? '';
    $apellidoP = $data['apellidoP'] ?? '';
    $apellidoM = $data['apellidoM'] ?? '';
    $telefono = $data['telefono'] ?? '';
    $email = $data['email'] ?? '';

    $apellidoPActual = $data['apellidoPActual'] ?? '';
    $apellidoMActual = $data['apellidoMActual'] ?? '';

    if (empty($apellidoPActual) || empty($apellidoMActual)) {
        echo json_encode([
            'success' => false,
            'mensaje' => 'Debe enviar los apellidos actuales para identificar al cliente'
        ]);
        exit;
    }

    try {
        $cliente = new Cliente(null, $nombre, $apellidoP, $apellidoM, $telefono, $email);
        $resultado = $daoCliente->actualizarCliente($cliente, $apellidoPActual, $apellidoMActual);

        if ($resultado) {
            echo json_encode([
                'success' => true,
                'mensaje' => 'Cliente actualizado correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Error al actualizar cliente. Verifica que los apellidos actuales sean correctos.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'mensaje' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Método HTTP no soportado'
    ]);
}
?>
