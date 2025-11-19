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

    if ($method === 'POST') {
        //Agregar nuevo cliente
        $data = json_decode(file_get_contents("php://input"), true);

        $nombre = $data['nombre'] ?? '';
        $apellidoP = $data['apellidoP'] ?? '';
        $apellidoM = $data['apellidoM'] ?? '';
        $telefono = $data['telefono'] ?? '';
        $email = $data['email'] ?? '';

        try {
            $cliente = new Cliente(null, $nombre, $apellidoP, $apellidoM, $telefono, $email);
            $resultado = $daoCliente->agregarNuevoCliente($cliente);

            if($resultado){
                echo json_encode([
                    'success' => true,
                    'mensaje' => 'Cliente agregado correctamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'mensaje' => 'Error al agregar cliente'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'mensaje' => $e->getMessage()
            ]);
        }
        
    } elseif ($method === 'GET') {
        if(isset($_GET['idCliente'])){
            $idCliente = intval($_GET['idCliente']);
            try {
                $reservas = $daoCliente->listarReservacionesDeCliente($idCliente);
                echo json_encode([
                    'success' => true,
                    'reservas' => $reservas
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => $e->getMessage()
                ]);
            }
        } else {
            try {
                $clientes = $daoCliente->listarClientes();
                echo json_encode([
                    'success' => true,
                    'clientes' => array_map(function($c){
                        return [
                            'id' => $c->getIdCliente(),
                            'nombre' => $c->getNombre(),
                            'apellidoP' => $c->getApellidoPaterno(),
                            'apellidoM' => $c->getApellidoMaterno(),
                            'telefono' => $c->getTelefono(),
                            'email' => $c->getEmail()
                        ];
                    }, $clientes)
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'mensaje' => $e->getMessage()
                ]);
            }
        }
    } else {
        echo json_encode([
            'success' => false,
            'mensaje' => 'Método HTTP no soportado'
        ]);
    }
?>
