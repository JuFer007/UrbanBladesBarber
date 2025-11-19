<?php
require_once(__DIR__ . '/../conexionBD_MySQL.php');
require_once(__DIR__ . '/../modelos/Reserva.php');
require_once(__DIR__ . '/../dao/DAO_Barbero.php');

class DAO_Reserva {
    //Agregar una nueva reserva
    public function agregarNuevaReserva($reserva) {
        $conexion = conexionPHP();
        $daoBarbero = new DAO_Barbero();
        $totalBarberos = $daoBarbero->obtenerCantidadBarberos();

        $fechaReserva = $reserva->getFechaReserva();
        $hora = $reserva->getHora();

        $barberoAsignado = null;
        for ($idBarbero = 1; $idBarbero <= $totalBarberos; $idBarbero++) {
            if ($this->hayDisponibilidad($idBarbero, $fechaReserva, $hora)) {
                $barberoAsignado = $idBarbero;
                break;
            }
        }

        if ($barberoAsignado === null) {
            return false;
        }

        $sql = "INSERT INTO reserva (idCliente, idBarbero, idServicio, fechaReserva, hora, estado) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);

        $idCliente = $reserva->getIdCliente();
        $idServicio = $reserva->getIdServicio();
        $estado = $reserva->getEstado();

        mysqli_stmt_bind_param(
            $stmt,
            "iiisss",
            $idCliente,
            $barberoAsignado,
            $idServicio,
            $fechaReserva,
            $hora,
            $estado
        );
        return mysqli_stmt_execute($stmt);
    }

    //Listar todas las reservas
    public function listarReservasPorFecha($fechaSeleccionada) {
        $conexion = conexionPHP();
        $sql = "SELECT reserva.idReserva,
                    CONCAT(cliente.nombreCliente, ' ', cliente.apellidoPaterno, ' ', cliente.apellidoMaterno) AS Cliente,
                    servicio.nombreServicio,
                    CONCAT(empleado.nombreEmpleado, ' ', empleado.apellidoPaternoE, ' ', empleado.apellidoMaternoE) AS Barbero,
                    reserva.fechaReserva, reserva.hora, reserva.Estado
                FROM cliente
                INNER JOIN reserva ON reserva.idCliente = cliente.idCliente
                INNER JOIN servicio ON servicio.idServicio = reserva.idServicio
                INNER JOIN barbero ON reserva.idBarbero = barbero.idBarbero
                INNER JOIN empleado ON empleado.idEmpleado = barbero.idEmpleado
                WHERE reserva.fechaReserva = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('s', $fechaSeleccionada);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $reservas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $reservas[] = $fila;
        }

        return $reservas;
    }

    //Listar reservas de un barbero 
    public function listarReservasID($idBarbero) {
        $conexion = conexionPHP();
        $sql = "SELECT reserva.idReserva, cliente.nombreCliente, concat(cliente.apellidoPaterno, ' ', cliente.apellidoMaterno) as Apellidos, 
        reserva.fechaReserva, reserva.hora, servicio.nombreServicio, reserva.estado 
        from cliente inner join reserva on cliente.idCliente = reserva.idCliente inner join servicio on reserva.idServicio = servicio.idServicio 
        where idBarbero = ?
        order by fechaReserva, hora asc";

        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param("i", $idBarbero);

            $stmt->execute();
            $resultado = $stmt->get_result();

            $reservas = [];
            while ($row = $resultado->fetch_assoc()) {
                $reservas[] = $row;
            }
            $stmt->close();
            return $reservas;
        } else {
            throw new Exception("Error en la consulta de reservas: " . $conexion->error);
            return [];
        }
    }

    //Cancelar una reserva
    public function cancelarReserva($idReserva){
        $conexion = conexionPHP();
        $sql = "UPDATE reserva set estado = 'Cancelada' where idReserva = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idReserva);
        $resultado = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $resultado;
    }

    //Cambiar estado de reserva a completada
    public function cambiarEstado($idReserva){
        $conexion = conexionPHP();
        $sql = "UPDATE reserva SET estado = 'Completada' WHERE idReserva = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idReserva);
        $resultado = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $resultado;
    }

    //Obtener ultimo id de reserva
    public function obtenerUltimoID() {
        $conexion = conexionPHP();
        $sql = "SELECT idReserva FROM reserva ORDER BY idReserva DESC LIMIT 1";
        $resultado = mysqli_query($conexion, $sql);

        if ($fila = mysqli_fetch_assoc($resultado)) {
            $ultimoID = (int)$fila['idReserva'];
            return $ultimoID;
        } else {
            return 0;
        }
    }

    //Verificar disponibilidad de reserva
    public function hayDisponibilidad($idBarbero, $fechaReserva, $hora) {
        $conexion = null;
        $stmt = null;
        try {
            $conexion = conexionPHP();
            // Normalizar hora a formato HH:MM:SS si es necesario
            if (strlen($hora) === 5) {
                $hora .= ':00';
            }
                        
            // Verificar si ya existe una reserva para ese barbero en esa fecha y hora
            $sql = "SELECT COUNT(*) as total FROM reserva 
                    WHERE idBarbero = ? 
                    AND fechaReserva = ? 
                    AND hora = ? 
                    AND estado != 'Cancelada'";
            
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "iss", $idBarbero, $fechaReserva, $hora);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            $fila = mysqli_fetch_assoc($resultado);
            
            $disponible = $fila['total'] == 0;
            return $disponible;
        } finally {
            if ($stmt) mysqli_stmt_close($stmt);
            if ($conexion) mysqli_close($conexion);
        }
    }

    //Editar una reserva
    public function editarReserva($idReserva, $nuevaFecha, $nuevaHora) {
        error_log("=== INICIO editarReserva ===");
        error_log("ID Reserva: $idReserva");
        error_log("Nueva Fecha: $nuevaFecha");
        error_log("Nueva Hora: $nuevaHora");
        
        $conexion = conexionPHP();
        mysqli_begin_transaction($conexion);

        try {
            $sqlActual = "SELECT idBarbero, fechaReserva, hora FROM reserva WHERE idReserva = ? FOR UPDATE";
            $stmtActual = mysqli_prepare($conexion, $sqlActual);
            mysqli_stmt_bind_param($stmtActual, "i", $idReserva);
            mysqli_stmt_execute($stmtActual);
            $reservaActual = mysqli_stmt_get_result($stmtActual)->fetch_assoc();
            mysqli_stmt_close($stmtActual);

            if (!$reservaActual) {
                throw new Exception("No se encontró la reserva $idReserva");
            }


            $idBarberoAsignado = $reservaActual['idBarbero'];
            $fechaActual = $reservaActual['fechaReserva'];
            $horaActual = $reservaActual['hora'];

            if (strlen($nuevaHora) === 5) {
                $nuevaHora .= ':00';
            }

            if ($nuevaFecha != $fechaActual || $nuevaHora != $horaActual) {
                
                $sqlVerificar = "SELECT COUNT(*) as total FROM reserva WHERE idBarbero = ? AND fechaReserva = ? AND hora = ? AND estado != 'Cancelada' AND idReserva != ?";
                $stmtVerificar = mysqli_prepare($conexion, $sqlVerificar);
                mysqli_stmt_bind_param($stmtVerificar, "issi", $idBarberoAsignado, $nuevaFecha, $nuevaHora, $idReserva);
                mysqli_stmt_execute($stmtVerificar);
                $hayConflicto = mysqli_stmt_get_result($stmtVerificar)->fetch_assoc()['total'] > 0;
                mysqli_stmt_close($stmtVerificar);
                
                if ($hayConflicto) {
                    $daoBarbero = new DAO_Barbero();
                    $totalBarberos = $daoBarbero->obtenerCantidadBarberos();
                    $nuevoBarberoEncontrado = null;
                    
                    for ($idBarberoCandidato = 1; $idBarberoCandidato <= $totalBarberos; $idBarberoCandidato++) {
                        $sqlCheck = "SELECT COUNT(*) as total FROM reserva WHERE idBarbero = ? AND fechaReserva = ? AND hora = ? AND estado != 'Cancelada'";
                        $stmtCheck = mysqli_prepare($conexion, $sqlCheck);
                        mysqli_stmt_bind_param($stmtCheck, "iss", $idBarberoCandidato, $nuevaFecha, $nuevaHora);
                        mysqli_stmt_execute($stmtCheck);
                        $estaOcupado = mysqli_stmt_get_result($stmtCheck)->fetch_assoc()['total'] > 0;
                        mysqli_stmt_close($stmtCheck);

                        if (!$estaOcupado) {
                            $nuevoBarberoEncontrado = $idBarberoCandidato;
                            break;
                        }
                    }
                    
                    if ($nuevoBarberoEncontrado === null) {
                        throw new Exception("No hay barberos disponibles para $nuevaFecha a las $nuevaHora");
                    }
                    
                    $idBarberoAsignado = $nuevoBarberoEncontrado;
                } else {
                }
            } else {
            }

            $sql = "UPDATE reserva SET fechaReserva = ?, hora = ?, idBarbero = ? WHERE idReserva = ?";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "ssii", $nuevaFecha, $nuevaHora, $idBarberoAsignado, $idReserva);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error MySQL al actualizar: " . mysqli_stmt_error($stmt));
            }
            
            mysqli_stmt_close($stmt);

            mysqli_commit($conexion);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($conexion);
            return false;
        } finally {
            mysqli_close($conexion);
        }
    }
}   


?>
