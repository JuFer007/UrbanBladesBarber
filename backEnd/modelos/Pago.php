<?php
    class Pago {
        //Atributos
        private $idPago;
        private $idReserva;
        private $montoPago;
        private $metodo;
        private $fechaPago;
        private $estadoPago;

        //Constructor
        public function __construct($idPago, $idReserva, $montoPago, $metodo, $fechaPago, $estadoPago) {
            $this->idPago = $idPago;
            $this->idReserva = $idReserva;
            $this->montoPago = $montoPago;
            $this->metodo = $metodo;
            $this->fechaPago = $fechaPago;
            $this->estadoPago = $estadoPago;
        }

        //Getters
        public function getIdPago() {
            return $this->idPago;
        }

        public function getIdReserva() {
            return $this->idReserva;
        }

        public function getMontoPago() {
            return $this->montoPago;
        }

        public function getMetodo() {
            return $this->metodo;
        }

        public function getFechaPago() {
            return $this->fechaPago;
        }

        //Setters
        public function setIdPago($idPago) {
            $this->idPago = $idPago;
        }

        public function setIdReserva($idReserva) {
            $this->idReserva = $idReserva;
        }

        public function setMontoPago($montoPago) {
            if ($montoPago >= 0) {
                $this->montoPago = $montoPago;
            }
        }

        public function setMetodo($metodo) {
            $this->metodo = $metodo;
        }

        public function setFechaPago($fechaPago) {
            $this->fechaPago = $fechaPago;
        }

        public function setEstado($estadoPago) {
            $this->estadoPago = $estadoPago;
        }

        public function getEstado() {
            return $this->estadoPago;
        }
    }
?>
