<?php
require_once(__DIR__ . '/../conexionBD_MySQL.php');
require_once(__DIR__ . '/../modelos/Administrador.php');

class DAO_Administrador {
    //Agregar un nuevo administrador
    public function agregarNuevoAdministrador($admin) {
        $conexion = conexionPHP();

        $sqlEmpleado = "INSERT INTO EMPLEADO 
            (nombreEmpleado, dni, apellidoPaternoE, apellidoMaternoE, telefono, salario, cargo, estadoE, generoE) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmtEmp = mysqli_prepare($conexion, $sqlEmpleado);
        if (!$stmtEmp) {
            throw new Exception("Error al preparar la consulta EMPLEADO: " . mysqli_error($conexion));
        }

        $nombre    = $admin->getNombreE();
        $dni       = $admin->getDNIempleado();
        $apellidoP = $admin->getApellidoPaternoE();
        $apellidoM = $admin->getApellidoMaternoE();
        $telefono  = $admin->getTelefono();
        $salario   = $admin->getSalario();
        $cargo     = $admin->getCargo();
        $estado    = $admin->getEstadoEmpleado();
        $genero    = $admin->getGenero();

        mysqli_stmt_bind_param(
            $stmtEmp,
            "sssssdsss",
            $nombre,
            $dni,
            $apellidoP,
            $apellidoM,
            $telefono,
            $salario,
            $cargo,
            $estado,
            $genero
        );

        mysqli_stmt_execute($stmtEmp);

        $idEmpleado = mysqli_insert_id($conexion);

        $sqlAdmin = "INSERT INTO ADMINISTRADOR (idEmpleado) VALUES (?)";
        $stmtAdmin = mysqli_prepare($conexion, $sqlAdmin);
        if (!$stmtAdmin) {
            throw new Exception("Error al preparar la consulta ADMINISTRADOR: " . mysqli_error($conexion));
        }

        mysqli_stmt_bind_param($stmtAdmin, "i", $idEmpleado);
        mysqli_stmt_execute($stmtAdmin);

        return $idEmpleado;
    }
}
?>
