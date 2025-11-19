<?php
    class Cliente {
        //Atributos
        private $idCliente;
        private $nombreCliente;
        private $apellidoPaterno;
        private $apellidoMaterno;
        private $telefono;
        private $email;

        //Constructor
        public function __construct($idCliente, $nombreCliente, $apellidoPaterno, $apellidoMaterno, $telefono, $email) {
            $this->idCliente = $idCliente;
            $this->nombreCliente = $nombreCliente;
            $this->apellidoPaterno = $apellidoPaterno;
            $this->apellidoMaterno = $apellidoMaterno;
            $this->telefono = $telefono;
            $this->email = $email;
        }

        //Getters
        public function getIdCliente() {
            return $this->idCliente;
        }

        public function getNombre() {
            return $this->nombreCliente;
        }

        public function getApellidoPaterno() {
            return $this->apellidoPaterno;
        }

        public function getApellidoMaterno() {
            return $this->apellidoMaterno;
        }

        public function getTelefono() {
            return $this->telefono;
        }

        public function getEmail() {
            return $this->email;
        }

        //Setters
        public function setNombre($nombreCliente) {
            $this->nombreCliente = $nombreCliente;
        }

        public function setApellidoPaterno($apellidoPaterno) {
            $this->apellidoPaterno = $apellidoPaterno;
        }

        public function setApellidoMaterno($apellidoMaterno) {
            $this->apellidoMaterno = $apellidoMaterno;
        }

        public function setTelefono($telefono) {
            $this->telefono = $telefono;
        }

        public function setEmail($email){
            $this->email = $email;
        }

        public function setIdCliente($idCliente) {
            $this->idCliente = $idCliente;
        }
    }
?>
