<?php
require_once(__DIR__ . '/../conexionBD_MySQL.php');
require_once(__DIR__ . '/../modelos/Cliente.php');

class DAO_Cliente {

    //Agregar un nuevo cliente
    public function agregarNuevoCliente($cliente) {
        $conexion = conexionPHP();
        $sql = "INSERT INTO CLIENTE (nombreCliente, apellidoPaterno, apellidoMaterno, telefono, email) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);

        $nombre = $cliente->getNombre();
        $apellidoP = $cliente->getApellidoPaterno();
        $apellidoM = $cliente->getApellidoMaterno();
        $telefono = $cliente->getTelefono();
        $email = $cliente->getEmail();

        mysqli_stmt_bind_param(
            $stmt,
            "sssss",
            $nombre,
            $apellidoP,
            $apellidoM,
            $telefono,
            $email
        );

        return mysqli_stmt_execute($stmt);
    }

    //Listar todos los clientes
    public function listarClientes() {
        $conexion = conexionPHP();
        $sql = "SELECT * FROM CLIENTE";
        $resultado = mysqli_query($conexion, $sql);
        $clientes = [];

        while ($fila = mysqli_fetch_assoc($resultado)) {
            $cliente = new Cliente(
                $fila['idCliente'],
                $fila['nombreCliente'],
                $fila['apellidoPaterno'],
                $fila['apellidoMaterno'],
                $fila['telefono'],
                $fila['email']
            );
            $cliente->setIdCliente($fila['idCliente']);
            $clientes[] = $cliente;
        }
        return $clientes;
    }

    //para actualizar el cliente
    public function actualizarCliente($cliente, $apellidoPaternoActual, $apellidoMaternoActual) {
        $conexion = conexionPHP();

        $sql = "UPDATE CLIENTE 
                SET nombreCliente = ?, apellidoPaterno = ?, apellidoMaterno = ?, telefono = ?, email = ? 
                WHERE apellidoPaterno = ? AND apellidoMaterno = ?";

        $stmt = mysqli_prepare($conexion, $sql);

        $nombre = $cliente->getNombre();
        $apellidoP = $cliente->getApellidoPaterno();
        $apellidoM = $cliente->getApellidoMaterno();
        $telefono = $cliente->getTelefono();
        $email = $cliente->getEmail();

        mysqli_stmt_bind_param(
            $stmt,
            "sssssss",
            $nombre,
            $apellidoP,
            $apellidoM,
            $telefono,
            $email,
            $apellidoPaternoActual,
            $apellidoMaternoActual
        );
        return mysqli_stmt_execute($stmt);
    }

    //Listar reservas del cliente
    public function listarReservacionesDeCliente($idCliente){
        $conexion = conexionPHP();
        $sql = "SELECT servicio.nombreServicio, reserva.fechaReserva, reserva.hora, servicio.precio 
                FROM servicio 
                INNER JOIN reserva ON reserva.idServicio = servicio.idServicio 
                INNER JOIN cliente ON cliente.idCliente = reserva.idCliente
                WHERE cliente.idCliente = ?";

        $stmt = mysqli_prepare($conexion, $sql);
        if(!$stmt) return [];

        // "i" porque idCliente es un entero
        mysqli_stmt_bind_param($stmt, "i", $idCliente);

        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);
        $reservas = [];
        while($fila = mysqli_fetch_assoc($resultado)){
            $reservas[] = $fila;
        }

        mysqli_stmt_close($stmt);

        return $reservas;
    }

    //Verificar cliente 
    public function verificarCliente($nombre, $apellidoPaterno, $apellidoMaterno){
        $conexion = conexionPHP();
        
        $sql = "SELECT idCliente 
                FROM cliente 
                WHERE nombreCliente = ? 
                AND apellidoPaterno = ? 
                AND apellidoMaterno = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sss", $nombre, $apellidoPaterno, $apellidoMaterno);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($fila = $resultado->fetch_assoc()) {
            return $fila['idCliente'];
        } else {
            return false;
        }
    }

    //Obtener ultimo id de cliente
    public function obtenerUltimoIDCliente() {
        $conexion = conexionPHP();
        $sql = "SELECT idCliente FROM Cliente ORDER BY idCliente DESC LIMIT 1";
        $resultado = mysqli_query($conexion, $sql);

        if ($fila = mysqli_fetch_assoc($resultado)) {
            $ultimoID = (int)$fila['idCliente'];
            return $ultimoID;
        } else {
            return 0;
        }
    }
}
?>
