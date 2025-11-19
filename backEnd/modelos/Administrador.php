<?php
require_once __DIR__ . '/Empleado.php';

class Administrador extends Empleado {
    //Atributos
    private $idAdministrador;

    //Constructor
    public function __construct($nombreEmpleado, $dni, $apellidoPaternoE, $apellidoMaternoE, $telefono, $salario, $cargo, $estadoEmpleado, $generoEmpleado) {
        parent::__construct($nombreEmpleado, $dni, $apellidoPaternoE, $apellidoMaternoE, $telefono, $salario, $cargo, $estadoEmpleado, $generoEmpleado);
    }

    //Getters y Setters para idAdministrador
    public function getIdAdministrador() {
        return $this->idAdministrador;
    }

    public function setIdAdministrador($idAdministrador) {
        $this->idAdministrador = $idAdministrador;
    }
}
?>
