<?php
require_once(__DIR__ . '/../conexionBD_MySQL.php');
require_once(__DIR__ . '/../modelos/Pago.php');

class DAO_Pago {
    //Agregar un nuevo pago
    public function agregarNuevoPago($pago) {
        $conexion = conexionPHP();
        if (!$conexion) throw new Exception("No se pudo conectar a la base de datos");

        $sql = "INSERT INTO PAGO (idReserva, montoPago, metodo, fechaPago, estadoPago) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);

        if (!$stmt) {
            throw new Exception("Error en prepare (INSERT PAGO): " . mysqli_error($conexion));
        }

        $idReserva = $pago->getIdReserva();
        $montoPago = $pago->getMontoPago();
        $metodo    = null;
        $fechaPago = $pago->getFechaPago();
        $estado    = "Pendiente";

        mysqli_stmt_bind_param($stmt, "idsss",
            $idReserva,
            $montoPago,
            $metodo,
            $fechaPago,
            $estado
        );

        if (!mysqli_stmt_execute($stmt)) {
            $err = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            throw new Exception("Error al ejecutar INSERT en PAGO: " . $err);
        }
        mysqli_stmt_close($stmt);
        return true;
    }

    //Listar todos los pagos
    public function listarPagos() {
        $conexion = conexionPHP();
        $sql = "SELECT * FROM PAGO";
        $resultado = mysqli_query($conexion, $sql);
        $pagos = [];

        while ($fila = mysqli_fetch_assoc($resultado)) {
            $pago = new Pago(
                $fila['idPago'],
                $fila['idReserva'],
                $fila['montoPago'],
                $fila['metodo'],
                $fila['fechaPago'],
                $fila['estadoPago']
            );
            $pagos[] = $pago;
        }
        return $pagos;
    }

    //Procesar pago 
    public function procesarPago($idPago, $metodoPago) {
        $conexion = conexionPHP();
        $sql = "UPDATE PAGO SET estadoPago = 'Confirmado', metodo = ? WHERE idPago = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) {
            throw new Exception("Error en prepare (UPDATE PAGO): " . mysqli_error($conexion));
        }
        mysqli_stmt_bind_param($stmt, "si", $metodoPago, $idPago);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }
}
?>
