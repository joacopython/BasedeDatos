<?php
    function insertar_en_tabla($database, $tabla_nombre, $atributos_dicc) {
        try {
            // Extraer los nombres de las columnas y los valores correspondientes
            $columnas = array_keys($atributos_dicc); // Obtiene las claves, que son los nombres de las columnas
            $valores = array_values($atributos_dicc); // Obtiene los valores correspondientes

            // Inicia la transacción
            $database->beginTransaction();

            // Generar la lista de columnas separadas por comas
            $columnas_str = implode(',', $columnas); 

            // Generar la lista de placeholders (?), uno por cada valor
            $placeholders = implode(',', array_fill(0, count($valores), '?'));

            // Crear la consulta preparada con los nombres de columnas y placeholders
            $sql = "INSERT INTO $tabla_nombre ($columnas_str) VALUES ($placeholders);";


            // Preparar y ejecutar la sentencia
            $stmt = $database->prepare($sql);
            $stmt->execute($valores);

            // Confirmar la transacción
            $database->commit();

        } catch (Exception $e) {
            // Revertir la transacción si hay un error
            $database->rollBack();
            $errorCode = $e->getCode();
            if ($errorCode != 23505) {
                echo "Error al insertar en la tabla $tabla_nombre: " . $e->getMessage(). "\n";
            }
        }
    }
?>