<?php 

require_once __DIR__ . '/Empleado.php';


class Barbero extends Empleado {
    //Atributos 
    private $idBarbero;
    private $especialidad;

    //Constructor
    public function __construct($nombreEmpleado, $dni, $apellidoPaternoE, $apellidoMaternoE, $telefono, $salario, $cargo, $estadoEmpleado, $generoEmpleado, $especialidad) {
        parent::__construct($nombreEmpleado, $dni, $apellidoPaternoE, $apellidoMaternoE, $telefono, $salario, $cargo, $estadoEmpleado, $generoEmpleado);
        $this->especialidad = $especialidad;
    }

    // Getter y Setter
    public function getEspecialidad(){ 
        return $this->especialidad; 
    }

    public function setEspecialidad($especialidad) {         
        $this->especialidad = $especialidad; 
    }
}
?>
