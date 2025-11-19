<?php
require_once(__DIR__ . '/../dao/DAO_Servicio.php');
require_once(__DIR__ . '/../modelos/Servicios.php');

$daoServicio = new DAO_Servicio();
$servicios = $daoServicio->listarServicios();

$formato = $_GET['formato'] ?? 'html';

if ($formato === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    $opciones = [];
    foreach ($servicios as $servicio) {
        $opciones[] = [
            'id' => $servicio->getIdServicio(),
            'nombre' => $servicio->getNombreServicio()
        ];
    }
    echo json_encode($opciones);

} else if ($formato === 'json_full') {
    header('Content-Type: application/json; charset=utf-8');
    $serviciosArray = [];
    foreach ($servicios as $servicio) {
        $serviciosArray[] = [
            'idServicio' => $servicio->getIdServicio(),
            'nombreServicio' => $servicio->getNombreServicio(),
            'descripcion' => $servicio->getDescripcion(),
            'precio' => $servicio->getPrecio(),
            'imagenURL' => $servicio->getImagenURL(),
            'estadoS' => $servicio->getEstadoS()
        ];
    }
    echo json_encode($serviciosArray);

} else {
    header('Content-Type: text/html; charset=utf-8');
    foreach ($servicios as $servicio) {
        if (strtolower($servicio->getEstadoS()) === 'activo') {
            // Usamos htmlspecialchars para prevenir ataques XSS
            $nombre = htmlspecialchars($servicio->getNombreServicio());
            $idServicio = htmlspecialchars($servicio->getIdServicio());
            $descripcion = htmlspecialchars($servicio->getDescripcion());
            $imagen = htmlspecialchars($servicio->getImagenURL());
            
            
            echo "
            <div class='col'>
                <div class='card shadow-sm border-0 h-100'>
                    <img src='{$imagen}' class='card-img-top rounded-top' alt='{$nombre}' style='height: 250px; object-fit: cover;'>
                    <div class='card-body text-center'>
                        <h5 class='card-title fw-bold'>{$nombre}</h5>
                        <p class='card-text'>{$descripcion}</p>
                        <button class='btn btn-dark btn-sm mt-2 btn-reservar' data-bs-toggle='modal' data-bs-target='#reservasModal' data-servicio-id='{$idServicio}' data-servicio-nombre='{$nombre}'>
                            Reservar
                        </button>
                    </div>
                </div>
            </div>";
        }
    }
}
?>