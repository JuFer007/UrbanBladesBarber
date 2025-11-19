<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:8000');
header('Access-Control-Allow-Credentials: true');

if (isset($_SESSION['logueado']) && $_SESSION['logueado'] === true) {
    echo json_encode([
        'logueado' => true,
        'correoElectronico' => $_SESSION['correoElectronico'] ?? null,
        'nombreCliente' => $_SESSION['nombreCliente'] ?? 'Usuario', 
        'fotoPerfil' => $_SESSION['fotoPerfil'] ?? '',
        'idUsuario' => $_SESSION['idUsuario'] ?? null,
        'idCliente' => $_SESSION['idCliente'] ?? null
    ]);
} else {
    echo json_encode(['logueado' => false]);
}
?>
