<?php
class Empleado {
    // Atributos
    private $idEmpleado;
    private $nombreEmpleado;
    private $dni;
    private $apellidoPaternoE;
    private $apellidoMaternoE;
    private $telefono;
    private $salario;
    private $cargo;
    private $estadoEmpleado;
    private $generoEmpleado;

    //Constructor 
    public function __construct($nombreEmpleado, $dni, $apellidoPaternoE, $apellidoMaternoE, $telefono, $salario, $cargo, $estadoEmpleado, $generoEmpleado) {
        $this->nombreEmpleado = $nombreEmpleado;
        $this->dni = $dni;
        $this->apellidoPaternoE = $apellidoPaternoE;
        $this->apellidoMaternoE = $apellidoMaternoE;
        $this->telefono = $telefono;
        $this->salario = $salario;
        $this->cargo = $cargo;
        $this->estadoEmpleado = $estadoEmpleado;
        $this->generoEmpleado = $generoEmpleado;
    }

    //Getters
    public function getNombreE() {
        return $this->nombreEmpleado;
    }

    public function getApellidoPaternoE() {
        return $this->apellidoPaternoE;
    }

    public function getApellidoMaternoE() {
        return $this->apellidoMaternoE;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function getSalario() {
        return $this->salario;
    }

    public function getCargo() {
        return $this->cargo;
    }

    public function getEstadoEmpleado() {
        return $this->estadoEmpleado;
    }

    public function getDNIempleado() {
        return $this->dni;
    }

    public function getGenero() {
        return $this->generoEmpleado;
    }

    //Setters
    public function setNombreEmpleado($nombreEmpleado) {
        $this->nombreEmpleado = $nombreEmpleado;
    }

    public function setApellidoPaternoE($apellidoPaternoE) {
        $this->apellidoPaternoE = $apellidoPaternoE;
    }

    public function setApellidoMaternoE($apellidoMaternoE) {
        $this->apellidoMaternoE = $apellidoMaternoE;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function setSalario($salario) {
        $this->salario = $salario;
    }

    public function setCargo($cargo) {
        $this->cargo = $cargo;
    }

    public function setEstadoEmpleado($estadoEmpleado) {
        $this->estadoEmpleado = $estadoEmpleado;
    }

    public function setDNIempleado($dniEmpleado) {
        $this->dni = $dniEmpleado;
    }

    public function setGeneroEmpleado($generoE) {
        $this->generoEmpleado = $generoE;
    }
}
?>
