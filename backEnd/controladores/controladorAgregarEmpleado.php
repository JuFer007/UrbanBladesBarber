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

header('Content-Type: application/json');

require_once __DIR__ . '/../dao/DAO_Administrador.php';
require_once __DIR__ . '/../dao/DAO_Barbero.php';
require_once __DIR__ . '/../dao/DAO_Recepcionista.php';
require_once __DIR__ . '/../dao/DAO_UsuarioEmpleado.php';
require_once __DIR__ . '/../modelos/Usuario.php';
require_once __DIR__ . '/../modelos/Administrador.php';
require_once __DIR__ . '/../modelos/Barbero.php';
require_once __DIR__ . '/../modelos/Recepcionista.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $nombre = $data['nombre'] ?? '';
    $dni = $data['dni'] ?? '';
    $apellidoP = $data['apellidoP'] ?? '';
    $apellidoM = $data['apellidoM'] ?? '';
    $telefono = $data['telefono'] ?? '';
    $salario = $data['salario'] ?? '';
    $cargo = $data['cargo'] ?? '';
    $genero = $data['genero'] ?? '';
    $estadoEmpleado = "Activo";
    $especialidad = $data['especialidad'] ?? null;
    $turno = $data['turno'] ?? null;

    try {
        $idEmpleado = null;

        switch ($cargo) {
            case 'Administrador':
                $empleado = new Administrador(
                    $nombre,
                    $dni,
                    $apellidoP,
                    $apellidoM,
                    $telefono,
                    $salario,
                    $cargo,
                    $estadoEmpleado,
                    $genero
                );
                $dao = new DAO_Administrador();
                $idEmpleado = $dao->agregarNuevoAdministrador($empleado);
                break;

            case 'Barbero':
                $empleado = new Barbero(
                    $nombre,
                    $dni,
                    $apellidoP,
                    $apellidoM,
                    $telefono,
                    $salario,
                    $cargo,
                    $estadoEmpleado,
                    $genero,
                    $especialidad
                );
                $dao = new DAO_Barbero();
                $idEmpleado = $dao->agregarNuevoBarbero($empleado);
                break;

            case 'Recepcionista':
                $empleado = new Recepcionista(
                    $nombre,
                    $dni,
                    $apellidoP,
                    $apellidoM,
                    $telefono,
                    $salario,
                    $cargo,
                    $estadoEmpleado,
                    $genero,
                    $turno
                );
                $dao = new DAO_Recepcionista();
                $idEmpleado = $dao->agregarNuevoRecepcionista($empleado);
                break;

            default:
                throw new Exception("Cargo no válido");
        }

        // Crear usuario automáticamente
        $daoUsuario = new DAO_UsuarioEmpleado();
        $nombreUsuario = strtolower($nombre . '_' . strtolower($cargo));
        $contraseña = strtolower(substr($nombre, 0, 4) . '12345');
        $usuario = new Usuario(null, $idEmpleado, $nombreUsuario, $contraseña);
        $daoUsuario->agregarNuevoUsuario($usuario);

        echo json_encode([
            'success' => true,
            'mensaje' => 'Empleado agregado correctamente'
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'mensaje' => $e->getMessage()
        ]);
    }
}
?>
