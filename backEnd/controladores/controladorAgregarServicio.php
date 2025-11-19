<?php
header('Content-Type: application/json');

// Manejo de errores y excepciones para depuración
ini_set('display_errors', 0); // No mostrar errores directamente en la salida
error_reporting(E_ALL);

set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => "Excepción no controlada: " . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
});

require_once(__DIR__ . '/../dao/DAO_Servicio.php');
require_once(__DIR__ . '/../modelos/Servicios.php');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido.");
    }

    $nombreServicio = $_POST['nombreServicio'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $precio = $_POST['precio'] ?? null;
    $estado = $_POST['estado'] ?? 'Activo';
    $imagenURL = null;

    if (empty($nombreServicio) || empty($descripcion) || !isset($precio)) {
        echo json_encode([
            'success' => false,
            'message' => 'Todos los campos son obligatorios.'
        ]);
        exit;
    }

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../recursos/servicios/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid() . '-' . basename($_FILES['imagen']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
            // Guardamos la ruta relativa para usarla en el HTML
            $imagenURL = 'recursos/servicios/' . $fileName;
        } else {
            throw new Exception("Error al mover el archivo subido.");
        }
    } else {
        // Usar una imagen por defecto si no se sube una
        $imagenURL = 'recursos/placeholder_servicio.png';
    }

    $servicio = new Servicio(null, $nombreServicio, $descripcion, $precio, $imagenURL, $estado);

    $daoServicio = new DAO_Servicio();
    $resultado = $daoServicio->agregarNuevoServicio($servicio);

    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Servicio agregado correctamente.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo agregar el servicio.'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>