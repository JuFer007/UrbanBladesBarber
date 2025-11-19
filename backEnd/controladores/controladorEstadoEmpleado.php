<?php
    header('Content-Type: application/json');
    require_once __DIR__ . '/../dao/DAO_Empleado.php';

    try {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input || !isset($input['accion']) || !isset($input['dni'])) {
            echo json_encode([
                "success" => false,
                "mensaje" => "Parámetros insuficientes."
            ]);
            exit;
        }

        $dao = new DAO_Empleado();
        $dni = $input['dni'];
        $accion = $input['accion'];
        $resultado = false;

        if ($accion === "habilitar") {
            $resultado = $dao->habilitarEmpleado($dni);
        } elseif ($accion === "deshabilitar") {
            $resultado = $dao->deshabilitarEmpleado($dni);
        }

        if ($resultado) {
            echo json_encode([
                "success" => true,
                "mensaje" => "Empleado {$accion} correctamente."
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "mensaje" => "No se pudo {$accion} al empleado."
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "mensaje" => "Error en el servidor: " . $e->getMessage()
        ]);
    }
?>
