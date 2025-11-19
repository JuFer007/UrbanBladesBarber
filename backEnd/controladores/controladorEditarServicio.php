<?php
header('Content-Type: application/json');

ini_set('display_errors', 0);
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

    $idServicio = $_POST['idServicio'] ?? null;
    $nombreServicio = $_POST['nombreServicio'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $precio = $_POST['precio'] ?? null;
    $estado = $_POST['estado'] ?? null;

    if (empty($idServicio) || empty($nombreServicio) || empty($descripcion) || !isset($precio) || empty($estado)) {
        throw new Exception("Todos los campos son obligatorios.");
    }

    $daoServicio = new DAO_Servicio();
    $servicioExistente = $daoServicio->buscarPorId($idServicio);

    if (!$servicioExistente) {
        throw new Exception("El servicio a editar no existe.");
    }

    $imagenURL = $servicioExistente->getImagenURL(); // Mantener la imagen actual por defecto

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../recursos/servicios/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid() . '-' . basename($_FILES['imagen']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
            $imagenURL = 'recursos/servicios/' . $fileName;
        } else {
            throw new Exception("Error al mover el nuevo archivo de imagen.");
        }
    }

    $servicioActualizado = new Servicio($idServicio, $nombreServicio, $descripcion, $precio, $imagenURL, $estado);
    $resultado = $daoServicio->actualizarServicio($servicioActualizado);

    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Servicio actualizado correctamente.'
        ]);
    } else {
        throw new Exception("No se pudo actualizar el servicio en la base de datos.");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>