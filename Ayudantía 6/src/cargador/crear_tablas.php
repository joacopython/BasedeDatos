<?php
    include('../config/conexion.php');
    require('parametros_tablas.php');

    foreach($tablas_iniciales as $tabla => $atributos) {
        try {
            echo "Creando tipos ENUM...\n";
            $db->beginTransaction();
    
            // Definir todos los ENUM que se usarán
            $db->exec("CREATE TYPE estamento_enum AS ENUM ('Estudiante', 'Académico', 'Administrativo');");
            $db->exec("CREATE TYPE grado_academico_enum AS ENUM ('Licenciatura', 'Magíster', 'Doctor');");
            $db->exec("CREATE TYPE jerarquia_academica_enum AS ENUM ('Asistente', 'Asociado', 'Instructor', 'Titular', 'Sin Jerarquizar', 'Comisión Superior');");
            $db->exec("CREATE TYPE contrato_enum AS ENUM ('Full Time', 'Part Time', 'Honorario');");
            $db->exec("CREATE TYPE modalidad_enum AS ENUM ('Presencial', 'Online', 'Híbrida');");
            $db->exec("CREATE TYPE caracter_enum AS ENUM('Mínimo', 'Taller', 'Electivo', 'CTI', 'CSI');");
            $db->exec("CREATE TYPE calificacion_enum AS ENUM ('SO', 'MB', 'B', 'SU', 'I', 'M', 'MM', 'P', 'NP', 'EX', 'A', 'R');");
            $db->exec("CREATE TYPE convocatoria_enum AS ENUM ('JUL', 'AGO', 'DIC', 'MAR');");
    
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            echo "Error al crear los tipos ENUM: " . $e->getMessage();
        }
        
        try {
            echo "Creando tabla $tabla...\n";
            $db->beginTransaction();
            $createTableQuery = "CREATE TABLE IF NOT EXISTS $tabla ($atributos);";
            $db->exec($createTableQuery);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            echo "Error al crear la tabla $tabla: " . $e->getMessage();
        }
    }

?>