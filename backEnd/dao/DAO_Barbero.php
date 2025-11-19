<?php
require_once(__DIR__ . '/../conexionBD_MySQL.php');
require_once(__DIR__ . '/../modelos/Barbero.php');

class DAO_Barbero {
    //Agregar un nuevo barbero
    public function agregarNuevoBarbero($barbero) {
        $conexion = conexionPHP();

        $sqlEmpleado = "INSERT INTO EMPLEADO 
            (nombreEmpleado, dni, apellidoPaternoE, apellidoMaternoE, telefono, salario, cargo, estadoE, generoE) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmtEmp = mysqli_prepare($conexion, $sqlEmpleado);
        if (!$stmtEmp) {
            throw new Exception("Error al preparar la consulta EMPLEADO: " . mysqli_error($conexion));
        }

        $nombre = $barbero->getNombreE();
        $dni = $barbero->getDNIempleado();
        $apellidoP = $barbero->getApellidoPaternoE();
        $apellidoM = $barbero->getApellidoMaternoE();
        $telefono = $barbero->getTelefono();
        $salario = $barbero->getSalario();
        $cargo = $barbero->getCargo();
        $estado = $barbero->getEstadoEmpleado();
        $genero = $barbero->getGenero();

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

        $sqlBarbero = "INSERT INTO BARBERO (idEmpleado, especialidad) VALUES (?, ?)";
        $stmtBarbero = mysqli_prepare($conexion, $sqlBarbero);
        if (!$stmtBarbero) {
            throw new Exception("Error al preparar la consulta BARBERO: " . mysqli_error($conexion));
        }

        $especialidad = $barbero->getEspecialidad();

        mysqli_stmt_bind_param(
            $stmtBarbero,
            "is",
            $idEmpleado,
            $especialidad
        );

        mysqli_stmt_execute($stmtBarbero);

        return $idEmpleado; 
    }
    
    //Obtener cantidad de barberos
    public function obtenerCantidadBarberos() {
        $conexion = conexionPHP();
        $sql = "SELECT COUNT(*) AS total FROM barbero";
        $resultado = mysqli_query($conexion, $sql);

        if ($fila = mysqli_fetch_assoc($resultado)) {
            return (int)$fila['total'];
        } else {
            return 0;
        }
    }

    //Listar todos los barberos
    public function listarBarberos() {
        $conexion = conexionPHP();
        $sql = "SELECT concat(empleado.nombreEmpleado, ' ', empleado.apellidoPaternoE, ' ', empleado.apellidoMaternoE) as Barbero
        from barbero inner join empleado on empleado.idEmpleado = barbero.idEmpleado";
        $resultado = mysqli_query($conexion, $sql);
        $barberos = [];

        while ($fila = mysqli_fetch_assoc($resultado)) {
            $barberos[] = $fila['Barbero'];
        }

        mysqli_close($conexion);
        return $barberos;
    }
}
?>
