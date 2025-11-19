<?php
    class Usuario {
        //Atributos 
        private $idUsuario;
        private $idEmpleado;
        private $nombreUsuario;
        private $contraseña;

        //Constructor
        public function __construct($idUsuario, $idEmpleado, $nombreUsuario, $contraseña) {
            $this->idUsuario = $idUsuario;
            $this->idEmpleado = $idEmpleado;
            $this->nombreUsuario = $nombreUsuario;
            $this->contraseña = $contraseña;
        }

        //Getters
        public function getIdUsuario() {
            return $this->idUsuario;
        }

        public function getIdEmpleado() {
            return $this->idEmpleado;
        }

        public function getNombreUsuario() {
            return $this->nombreUsuario;
        }

        public function getContraseña() {
            return $this->contraseña;
        }

        //Setters
        public function setIdUsuario($idUsuario) {
            $this->idUsuario = $idUsuario;
        }

        public function setIdEmpleado($idEmpleado) {
            $this->idEmpleado = $idEmpleado;
        }

        public function setNombreUsuario($nombreUsuario) {
            $this->nombreUsuario = $nombreUsuario;
        }

        public function setContraseña($contraseña) {
            $this->contraseña = $contraseña;
        }
    }
?>
