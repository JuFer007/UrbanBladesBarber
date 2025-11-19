<?php
    class Reserva {
        //Atributos 
        private $idReserva;
        private $idCliente;
        private $idBarbero;
        private $idServicio;
        private $fechaReserva;
        private $hora;
        private $estado;

        //Constructor
        public function __construct($idReserva, $idCliente, $idBarbero, $idServicio, $fechaReserva, $hora, $estado) {
            $this->idReserva = $idReserva;
            $this->idCliente = $idCliente;
            $this->idBarbero = $idBarbero;
            $this->idServicio = $idServicio;
            $this->fechaReserva = $fechaReserva;
            $this->hora = $hora;
            $this->estado = $estado;
        }

        //Getters
        public function getIdReserva() {
            return $this->idReserva;
        }

        public function getIdCliente() {
            return $this->idCliente;
        }

        public function getIdBarbero() {
            return $this->idBarbero;
        }

        public function getIdServicio() {
            return $this->idServicio;
        }

        public function getFechaReserva() {
            return $this->fechaReserva;
        }

        public function getHora() {
            return $this->hora;
        }

        public function getEstado() {
            return $this->estado;
        }

        //Setters
        public function setIdReserva($idReserva) {
            $this->idReserva = $idReserva;
        }

        public function setIdCliente($idCliente) {
            $this->idCliente = $idCliente;
        }

        public function setIdBarbero($idBarbero) {
            $this->idBarbero = $idBarbero;
        }

        public function setIdServicio($idServicio) {
            $this->idServicio = $idServicio;
        }

        public function setFechaReserva($fechaReserva) {
            $this->fechaReserva = $fechaReserva;
        }

        public function setHora($hora) {
            $this->hora = $hora;
        }

        public function setEstado($estado) {
            $this->estado = $estado;
        }
    }
?>
