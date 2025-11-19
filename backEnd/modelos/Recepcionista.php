<?php

require_once __DIR__ . '/Empleado.php';

class Recepcionista extends Empleado {
    //Atributos 
    private $idRecepcionista;
    private $turno;

    //Constructor
    public function __construct($nombreEmpleado, $dni, $apellidoPaternoE, $apellidoMaternoE, $telefono, $salario, $cargo, $estadoEmpleado, $generoEmpleado, $turno) {
        parent::__construct($nombreEmpleado, $dni, $apellidoPaternoE, $apellidoMaternoE, $telefono, $salario, $cargo, $estadoEmpleado, $generoEmpleado);
        $this->turno = $turno;
    }

    //Getters y Setters
    public function getTurno() {
        return $this->turno;
    }

    public function setTurno($turno) {
        $this->turno = $turno;
    }
}
?>