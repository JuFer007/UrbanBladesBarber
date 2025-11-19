<?php
require_once(__DIR__ . '/../conexionBD_MySQL.php');
require_once(__DIR__ . '/../modelos/Empleado.php');

class DAO_Reporte{
    public function totalClientes() {
        $conexion = conexionPHP();  
        $sql = "SELECT COUNT(*) AS total FROM cliente;";  
        $resultado = mysqli_query($conexion, $sql);  

        if ($resultado) {
            $fila = mysqli_fetch_assoc($resultado);  
            return $fila['total'];  
        } else {
            return 0;
        }
    } 
    
    public function totalEmpleados() {
        $conexion = conexionPHP();  
        $sql = "SELECT count(*) as total from empleado;";  
        $resultado = mysqli_query($conexion, $sql);  

        if ($resultado) {
            $fila = mysqli_fetch_assoc($resultado);  
            return $fila['total'];  
        } else {
            return 0;
        }
    }
    
    public function cantidadReservas() {
        $conexion = conexionPHP();  
        $sql = "SELECT count(*) as total from reserva where reserva.estado != 'Cancelada';";  
        $resultado = mysqli_query($conexion, $sql);  

        if ($resultado) {
            $fila = mysqli_fetch_assoc($resultado);  
            return $fila['total'];  
        } else {
            return 0;
        }
    }

    public function totalGanancias(){
        $conexion = conexionPHP();  
        $sql = "SELECT sum(montoPago) as total from pago;";  
        $resultado = mysqli_query($conexion, $sql);  

        if ($resultado) {
            $fila = mysqli_fetch_assoc($resultado);  
            return $fila['total'];  
        } else {
            return 0;
        }
    }

    public function datosGrafico() {
        $conexion = conexionPHP();  

        $sql = "SELECT MONTH(fechaReserva) AS mes, COUNT(*) AS total
                FROM reserva
                WHERE YEAR(fechaReserva) = YEAR(CURDATE())
                GROUP BY MONTH(fechaReserva)
                ORDER BY MONTH(fechaReserva);";  

        $resultado = mysqli_query($conexion, $sql);  

        $labels = [];
        $totales = [];

        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        if ($resultado) {
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $mesNumero = (int)$fila['mes'];
                $labels[] = $meses[$mesNumero];
                $totales[] = (int)$fila['total'];
            }
        }

        return [
            'labels' => $labels,
            'totales' => $totales
        ];
    }

    public function ingresosPorMesyMetodo(){
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo',
            6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Setiembre', 10 => 'Octubre',
            11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $conexion = conexionPHP();  

        $sql = "SELECT 
                    MONTH(fechaPago) AS mes, 
                    metodo, 
                    SUM(montoPago) AS total
                FROM pago
                WHERE YEAR(fechaPago) = YEAR(CURDATE()) and estadoPago = 'Confirmado'
                GROUP BY MONTH(fechaPago), metodo
                ORDER BY MONTH(fechaPago), metodo;";

        $resultado = mysqli_query($conexion, $sql);

        if (!$resultado) {
            die("Error en la consulta: " . mysqli_error($conexion));
        }

        $tiposPago = ['Efectivo', 'Tarjeta', 'Yape', 'Plin'];
        $datasets = [];
        foreach($tiposPago as $tipo){
            $datasets[$tipo] = array_fill(1, 12, 0); 
        }

        while($row = mysqli_fetch_assoc($resultado)){
            $mes = (int)$row['mes'];
            $tipo = $row['metodo'];
            $total = (float)$row['total'];
            $datasets[$tipo][$mes] = $total;
        }

        $labels = [];
        foreach(range(1,12) as $m){
            $labels[] = $meses[$m];
        }

        $colores = [
            'Efectivo' => 'rgba(75, 192, 192, 1)',
            'Tarjeta' => 'rgba(255, 99, 132, 1)',
            'Yape' => 'rgba(54, 162, 235, 1)',
            'Plin' => 'rgba(255, 206, 86, 1)'
        ];

        $chartDatasets = [];
        foreach($datasets as $tipo => $valores){
            $chartDatasets[] = [
                'label' => $tipo,
                'data' => array_values($valores),
                'borderColor' => $colores[$tipo],
                'backgroundColor' => str_replace('1', '0.2', $colores[$tipo]),
                'fill' => true
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $chartDatasets
        ];
    }
}

?>
