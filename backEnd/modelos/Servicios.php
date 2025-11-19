<?php
    class Servicio {
        //Atributos
        private $idServicio;
        private $nombreServicio;
        private $descripcion;
        private $precio;
        private $imagenURL;
        private $estadoS;
        
        //Constructor
        public function __construct($idServicio, $nombreServicio, $descripcion, $precio, $imagenURL = '', $estadoS = 'Activo') {
            $this->idServicio = $idServicio;
            $this->nombreServicio = $nombreServicio;
            $this->descripcion = $descripcion;
            $this->precio = $precio;
            $this->imagenURL = $imagenURL;
            $this->estadoS = $estadoS;
        }

        //Getters
        public function getIdServicio() {
            return $this->idServicio;
        }

        public function getNombreServicio() {
            return $this->nombreServicio;
        }

        public function getDescripcion() {
            return $this->descripcion;
        }

        public function getPrecio() {
            return $this->precio;
        }

        public function getImagenURL() {
            return $this->imagenURL;
        }

        public function getEstadoS(){
            return $this->estadoS;
        }

        //Setters
        public function setIdServicio($idServicio) {
            $this->idServicio = $idServicio;
        }

        public function setNombreServicio($nombreServicio) {
            $this->nombreServicio = $nombreServicio;
        }

        public function setDescripcion($descripcion) {
            $this->descripcion = $descripcion;
        }

        public function setPrecio($precio) {
            if ($precio >= 0) {
                $this->precio = $precio;
            }
        }

        public function setImagenURL($imagenURL) {
            $this->imagenURL = $imagenURL;
        }

        public function setEstadoS($estadoS) {
            $this->estadoS = $estadoS;
        }

    }
?>
