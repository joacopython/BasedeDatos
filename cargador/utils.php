<?php
    function insertar_en_tabla($database, $tabla_nombre, $atributos_dicc) {
        try {
            $columnas = array_keys($atributos_dicc); 
            $valores = array_values($atributos_dicc); 

            $database->beginTransaction();

            $columnas_str = implode(',', $columnas); 

            $placeholders = implode(',', array_fill(0, count($valores), '?'));

            $sql = "INSERT INTO $tabla_nombre ($columnas_str) VALUES ($placeholders);";


            $stmt = $database->prepare($sql);
            $stmt->execute($valores);

            $database->commit();

        } catch (Exception $e) {
            $database->rollBack();
            $errorCode = $e->getCode();
            if ($errorCode != 23505) {
                echo "Error al insertar en la tabla $tabla_nombre: " . $e->getMessage(). "\n";
                print_r($atributos_dicc);
            }
        }
    }
?>