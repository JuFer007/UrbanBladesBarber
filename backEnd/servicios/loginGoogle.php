<?php
require_once __DIR__ . '/../conexionBD_MySQL.php';
require_once __DIR__ . '/../modelos/Cliente.php';
require_once __DIR__ . '/../modelos/UsuarioCliente.php';
require_once __DIR__ . '/../dao/DAO_UsuarioCliente.php';
require_once __DIR__ . '/../dao/DAO_Cliente.php';

$daoUser = new DAO_UsuarioCliente();
$daoCliente = new DAO_Cliente();

//Datos del usuario
$nombre = trim($_GET['nombreCliente'] ?? '');
$apellidoPaterno = trim($_GET['apellidoPaterno'] ?? '');
$apellidoMaterno = trim($_GET['apellidoMaterno'] ?? '');
$correoElectronico = trim($_GET['correoElectronico'] ?? '');
$contraseña = trim($_GET['contraseña'] ?? '');
$fotoPerfil = trim($_GET['fotoPerfil'] ?? '');

if (empty($correoElectronico)) {
    header("Location: http://localhost:8000/frontEnd/paginas/login.php?error=correo_vacio");
    exit;
}

//Verificar si usario esta registrado
if ($daoUser->verificarUsuario($correoElectronico)) {

    $datos = $daoUser->obtenerDatosUsuario($correoElectronico);

    if (!$datos) {
        header("Location: http://localhost:8000/frontEnd/paginas/login.php?error=datos_no_encontrados");
        exit;
    }

    $nombreCompletoBD = trim($datos['nombre'] . ' ' . $datos['apellidoPaterno'] . ' ' . $datos['apellidoMaterno']);

    //Iniciar sesión
    session_start();
    $_SESSION['logueado'] = true;
    $_SESSION['correoElectronico'] = $datos['correoElectronico'];
    $_SESSION['idUsuario'] = $datos['idUsuario'] ?? null;
    $_SESSION['idCliente'] = $datos['idCliente'] ?? null;
    $_SESSION['nombreCliente'] = $nombreCompletoBD ?: 'Usuario';
    $_SESSION['fotoPerfil'] = $datos['fotoPerfil'] ?? $fotoPerfil ?? '/recursos/default-avatar.png';

    header("Location: http://localhost:8000/index.html");
    exit;

} else {

    //Crear cliente
    $cliente = new Cliente(
        null,
        $nombre,
        $apellidoPaterno,
        $apellidoMaterno,
        null,
        $correoElectronico
    );
    $daoCliente->agregarNuevoCliente($cliente);

    //Obtener el ID recién insertado
    $idUltimoCliente = $daoCliente->obtenerUltimoIDCliente();

    //Crear usuario cliente
    $usuario = new UsuarioCliente(
        null,
        $idUltimoCliente,
        $correoElectronico,
        $contraseña,
        $fotoPerfil
    );
    $daoUser->agregarNuevoUsuarioCliente($usuario);

    $datos = $daoUser->obtenerDatosUsuario($correoElectronico);

    if (!$datos) {
        header("Location: http://localhost:8000/frontEnd/paginas/login.php?error=registro_fallido");
        exit;
    }

    $nombreCompletoBD = trim($datos['nombre'] . ' ' . $datos['apellidoPaterno'] . ' ' . $datos['apellidoMaterno']);

    session_start();
    $_SESSION['logueado'] = true;
    $_SESSION['correoElectronico'] = $datos['correoElectronico'];
    $_SESSION['idUsuario'] = $datos['idUsuario'] ?? null;
    $_SESSION['idCliente'] = $datos['idCliente'] ?? null;
    $_SESSION['nombreCliente'] = $nombreCompletoBD ?: 'Usuario';
    $_SESSION['fotoPerfil'] = $datos['fotoPerfil'] ?? '/recursos/default-avatar.png';

    header("Location: http://localhost:8000/index.html");
    exit;
}
?>
