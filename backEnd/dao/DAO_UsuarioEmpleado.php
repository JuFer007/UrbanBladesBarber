<?php
require_once(__DIR__ . '/../conexionBD_MySQL.php');
require_once(__DIR__ . '/../modelos/Usuario.php');

class DAO_UsuarioEmpleado {
    //Agregar un nuevo usuario
    public function agregarNuevoUsuario($usuario) {
        $conexion = conexionPHP();

        $sql = "INSERT INTO UsuarioEmpleados (idEmpleado, nombreUsuario, contraseña) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta USUARIO: " . mysqli_error($conexion));
        }

        $idEmpleado = $usuario->getIdEmpleado();
        $nombreUsuario = $usuario->getNombreUsuario();
        $contrasenia = $usuario->getContraseña();

        mysqli_stmt_bind_param(
            $stmt,
            "iss",
            $idEmpleado,
            $nombreUsuario,
            $contrasenia
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al ejecutar la consulta USUARIO: " . mysqli_error($conexion));
        }

        return mysqli_insert_id($conexion);
    }

    //para verificar el usuario
    public function verificarUsuario($nombreUsuario, $contrasenia) {
        $conexion = conexionPHP();

        $sql = "SELECT UsuarioEmpleados.nombreUsuario, UsuarioEmpleados.contraseña, empleado.estadoE, empleado.cargo
                FROM UsuarioEmpleados
                INNER JOIN Empleado ON UsuarioEmpleados.idEmpleado = empleado.idEmpleado
                WHERE UsuarioEmpleados.nombreUsuario = ? AND UsuarioEmpleados.contraseña = ?";

        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta de verificación: " . mysqli_error($conexion));
        }

        mysqli_stmt_bind_param($stmt, "ss", $nombreUsuario, $contrasenia);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al ejecutar la consulta de verificación: " . mysqli_error($conexion));
        }

        $resultado = mysqli_stmt_get_result($stmt);

        if ($fila = mysqli_fetch_assoc($resultado)) {
            return [
                "valido"  => true,
                "estado"  => $fila['estadoE'],
                "usuario" => $fila['nombreUsuario'],
                "cargo"   => $fila['cargo']
            ];
        } else {
            return [
                "valido"  => false,
                "estado"  => null,
                "usuario" => null,
                "cargo"   => null
            ];
        }
    }

    //Obtener nombre, apellido y cargo por nombre de usuario
    public function obtenerNombreCompletoCargoPorUsuario($nombreUsuario) {
        $conexion = conexionPHP();
        $sql = "SELECT empleado.nombreEmpleado, empleado.apellidoPaternoE, empleado.cargo, empleado.generoE
                FROM empleado
                INNER JOIN UsuarioEmpleados ON UsuarioEmpleados.idEmpleado = empleado.idEmpleado
                WHERE UsuarioEmpleados.nombreUsuario = ?";

        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "s", $nombreUsuario);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if ($fila = mysqli_fetch_assoc($resultado)) {
            $nombreCompleto = $fila['nombreEmpleado'] . ' ' . $fila['apellidoPaternoE'];

            return [
                'nombreCompleto' => $nombreCompleto,
                'cargo'          => $fila['cargo'],
                'genero'         => $fila['generoE']
            ];
        } else {
            return null;
        }
    }

    //obtener id del barbero
    public function obtenerIdEmpleadoPorUsuario($nombreUsuario) {
        $conexion = conexionPHP(); 

        $sql = "SELECT barbero.idBarbero from empleado 
        inner join UsuarioEmpleados on empleado.idEmpleado = UsuarioEmpleados.idEmpleado 
        inner join barbero on barbero.idEmpleado = empleado.idEmpleado
        where UsuarioEmpleados.nombreUsuario = ?";

        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param("s", $nombreUsuario);

            $stmt->execute();

            $resultado = $stmt->get_result();
            $idBarbero = null;

            if ($row = $resultado->fetch_assoc()) {
                $idBarbero = $row['idBarbero'];
            }

            $stmt->close();
            return $idBarbero; 
        } else {
            echo "Error en la consulta: " . $conexion->error;
            return null;
        }
    }
}
?>
