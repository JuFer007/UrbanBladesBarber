<?php
require_once(__DIR__ . '/../conexionBD_MySQL.php');
require_once(__DIR__ . '/../modelos/Servicios.php');

class DAO_Servicio {

    //Agregar un nuevo servicio
    public function agregarNuevoServicio($servicio) {
        $conexion = conexionPHP();
        $sql = "INSERT INTO SERVICIO (nombreServicio, descripcion, precio, imagenURL, estadoS) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta de inserción: " . mysqli_error($conexion));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "ssdss",
            $servicio->getNombreServicio(),
            $servicio->getDescripcion(),
            $servicio->getPrecio(),
            $servicio->getImagenURL(),
            $servicio->getEstadoS()
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al ejecutar la consulta de inserción: " . mysqli_stmt_error($stmt));
        }

        return true;
    }

    //Listar todos los servicios
    public function listarServicios() {
        $conexion = conexionPHP();
        $sql = "SELECT * FROM SERVICIO";
        $resultado = mysqli_query($conexion, $sql);
        $servicios = [];

        while ($fila = mysqli_fetch_assoc($resultado)) {
            $servicio = new Servicio(
                $fila['idServicio'],
                $fila['nombreServicio'],
                $fila['descripcion'],
                $fila['precio'],
                $fila['imagenURL'] ?? '',
                $fila['estadoS']
            );
            $servicios[] = $servicio;
        }

        return $servicios;
    }


    //Buscar un servicio por ID
    public function buscarPorId($idServicio) {
        $conexion = conexionPHP();
        $sql = "SELECT * FROM SERVICIO WHERE idServicio = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idServicio);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if ($fila = mysqli_fetch_assoc($resultado)) {
            return new Servicio(
                $fila['idServicio'],
                $fila['nombreServicio'],
                $fila['descripcion'],
                $fila['precio'],
                $fila['imagenURL'] ?? '',
                $fila['estadoS']
            );
        }

        return null;
    }

    //Actualizar un servicio
    public function actualizarServicio($servicio) {
        $conexion = conexionPHP();
        $sql = "UPDATE SERVICIO SET nombreServicio = ?, descripcion = ?, precio = ?, imagenURL = ?, estadoS = ? WHERE idServicio = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta de actualización: " . mysqli_error($conexion));
        }
        mysqli_stmt_bind_param(
            $stmt,
            "ssdssi",
            $servicio->getNombreServicio(),
            $servicio->getDescripcion(),
            $servicio->getPrecio(),
            $servicio->getImagenURL(),
            $servicio->getEstadoS(),
            $servicio->getIdServicio()
        );
        return mysqli_stmt_execute($stmt);
    }

    //Eliminar un servicio
    public function eliminarServicio($idServicio) {
        $conexion = conexionPHP();
        $sql = "DELETE FROM SERVICIO WHERE idServicio = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idServicio);
        return mysqli_stmt_execute($stmt);
    }

    public function deshabilitarServicio($idServicio) {
        $conexion = conexionPHP();
        $sql = "UPDATE servicio SET estadoS = 'Inactivo' WHERE idServicio = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idServicio);
        return mysqli_stmt_execute($stmt);
    }

    //Habilitar un servicio por ID
    public function habilitarServicio($idServicio) {
        $conexion = conexionPHP();
        $sql = "UPDATE servicio SET estadoS = 'Activo' WHERE idServicio = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idServicio);
        return mysqli_stmt_execute($stmt);
    }

    //Obtener monto de un servicio
    public function obtenerMontoServicio($nombreServicio) {
        $sql = "SELECT precio FROM servicio WHERE nombreServicio = ?";
        $conexion = conexionPHP();
        $stmt = mysqli_prepare($conexion, $sql);

        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, "s", $nombreServicio);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        if ($fila = mysqli_fetch_assoc($resultado)) {
            return $fila['precio'];
        } else {
            return null;
        }
    }
}
?>
