<?php
    conexionPHP();
    function conexionPHP(){
        $server = 'localhost';
        $user = 'root';
        $password = '123456';
        $database = 'barberia';
        $conectar = mysqli_connect($server, $user, $password, $database)or die("Error en la conexión");
        return $conectar;
    }
?>
