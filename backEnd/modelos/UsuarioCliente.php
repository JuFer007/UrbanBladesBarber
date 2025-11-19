<?php

class UsuarioCliente {
    private $idUsuario;
    private $idCliente;
    private $correoElectronico;
    private $contraseña;
    private $fotoPerfil;

    //Constructor
    public function __construct($idUsuario, $idCliente, $correoElectronico, $contraseña, $fotoPerfil = '') {
        $this->idUsuario = $idUsuario;
        $this->idCliente = $idCliente;
        $this->correoElectronico = $correoElectronico;
        $this->contraseña = $contraseña;
        $this->fotoPerfil = $fotoPerfil;
    }

    //Getters y setters
    public function getIdUsuario() {
        return $this->idUsuario;
    }

    public function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    public function getIdCliente() {
        return $this->idCliente;
    }

    public function setIdCliente($idCliente) {
        $this->idCliente = $idCliente;
    }

    public function getCorreoElectronico() {
        return $this->correoElectronico;
    }

    public function setCorreoElectronico($correoElectronico) {
        $this->correoElectronico = $correoElectronico;
    }

    public function getContraseña() {
        return $this->contraseña;
    }

    public function setContraseña($contraseña) {
        $this->contraseña = $contraseña;
    }

    public function getFotoPerfil() {
        return $this->fotoPerfil;
    }

    public function setFotoPerfil($fotoPerfil) {
        $this->fotoPerfil = $fotoPerfil;
    }
}
?>
