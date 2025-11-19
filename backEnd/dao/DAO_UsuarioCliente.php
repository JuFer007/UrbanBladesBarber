<?php

require_once(__DIR__ . '/../conexionBD_MySQL.php');
require_once(__DIR__ . '/../modelos/UsuarioCliente.php');

class DAO_UsuarioCliente {
    //Agregar nuevo usuario
    public function agregarNuevoUsuarioCliente($usuarioCliente) {
        $conexion = conexionPHP();

        $sql = "INSERT INTO usuarioclientes (idCliente, correoElectronico, contraseña, fotoPerfil) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta USUARIO: " . mysqli_error($conexion));
        }

        $idCliente = $usuarioCliente->getIdCliente();
        $correoElectronico = $usuarioCliente->getCorreoElectronico();
        $contraseña = $usuarioCliente->getContraseña();
        $fotoPerfil = $usuarioCliente->getFotoPerfil();

        mysqli_stmt_bind_param(
            $stmt,
            "isss",
            $idCliente,
            $correoElectronico,
            $contraseña,
            $fotoPerfil
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al ejecutar la consulta USUARIO: " . mysqli_error($conexion));
        }
        return mysqli_insert_id($conexion);
    }
    
    //Credenciales de usuario cliente
    public function validarInicioSesionCliente($correoElectronico, $contrasenia) {
        $conexion = conexionPHP();
        $sql = "SELECT uc.idUsuario, uc.correoElectronico, uc.contraseña
                FROM usuarioclientes AS uc
                WHERE uc.correoElectronico = ? and uc.contraseña = ?";

        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
        }

        mysqli_stmt_bind_param($stmt, "s", $correoElectronico);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al ejecutar la consulta: " . mysqli_error($conexion));
        }

        $resultado = mysqli_stmt_get_result($stmt);

        if ($fila = mysqli_fetch_assoc($resultado)) {
            if ($fila['contraseña'] === $contrasenia) {
                return [
                    "valido"   => true,
                    "idUsuario"=> $fila['idUsuario'],
                    "correo"   => $fila['correoElectronico'],
                    "nombre"   => $fila['nombreCliente'],
                    "apellido" => $fila['apellidoCliente']
                ];
            }
        }
        return [
            "valido"   => false,
            "idUsuario"=> null,
            "correo"   => null,
            "nombre"   => null,
            "apellido" => null
        ];
    }

    //Verificar usuario existente
    public function verificarUsuario($correoElectronico) {
        $conexion = conexionPHP();

        $sql = "SELECT 1 FROM usuarioclientes WHERE correoElectronico = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "s", $correoElectronico);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $existe = mysqli_stmt_num_rows($stmt) > 0;

        mysqli_stmt_close($stmt);
        mysqli_close($conexion);

        return $existe;
    }   

    //Obtener datos completos del usuario y cliente
    public function obtenerDatosUsuario($correoElectronico) {
        $conexion = conexionPHP();

        $sql = "SELECT 
                    uc.idUsuario,
                    uc.idCliente,
                    uc.correoElectronico,
                    uc.fotoPerfil,
                    c.nombreCliente AS nombre,
                    c.apellidoPaterno AS apellidoPaterno,
                    c.apellidoMaterno AS apellidoMaterno
                FROM usuarioclientes uc
                INNER JOIN cliente c ON uc.idCliente = c.idCliente
                WHERE uc.correoElectronico = ?";

        $stmt = mysqli_prepare($conexion, $sql);
        if (!$stmt) {
            throw new Exception('Error al preparar la consulta: ' . mysqli_error($conexion));
        }

        mysqli_stmt_bind_param($stmt, "s", $correoElectronico);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($resultado) > 0) {
            $usuario = mysqli_fetch_assoc($resultado);
            mysqli_stmt_close($stmt);
            mysqli_close($conexion);
            return $usuario;
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conexion);
            return null;
        }
    }
}
?>
