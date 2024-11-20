<?php
include('../config/conexion.php');
require('parametros_tablas.php');

// Función para crear un tipo ENUM solo si no existe
function createEnumIfNotExists($db, $enumName, $enumValues) {
    try {
        // Verificar si el tipo ya existe
        $query = $db->prepare("SELECT 1 FROM pg_type WHERE typname = :enumName");
        $query->execute(['enumName' => $enumName]);

        
        if ($query->fetch()) {
            // Si el tipo ya existe, eliminarlo
            $db->exec("DROP TYPE $enumName CASCADE;");
            echo "Tipo ENUM '$enumName' eliminado.\n";
        }
        

        // Crear el tipo ENUM
        $db->exec("CREATE TYPE $enumName AS ENUM ($enumValues);");
        echo "Tipo ENUM '$enumName' creado.\n";
    } catch (Exception $e) {
        echo "Error al crear el tipo ENUM '$enumName': " . $e->getMessage() . "\n";
    }
}

$db->beginTransaction();
createEnumIfNotExists($db, 'estamento_enum', "'Estudiante', 'Académico', 'Administrativo'");
createEnumIfNotExists($db, 'jerarquia_academica_enum', "'Asistente', 'Asociado', 'Instructor', 'Titular', 'Sin Jerarquizar', 'Comisión Superior'");
createEnumIfNotExists($db, 'modalidad_enum', "'Presencial', 'OnLine', 'Híbrida'");
createEnumIfNotExists($db, 'caracter_enum', "'Mínimo', 'Taller', 'Electivo', 'CTI', 'CSI'");
createEnumIfNotExists($db, 'calificacion_enum', "'SO', 'MB', 'B', 'SU', 'I', 'M', 'MM', 'P', 'NP', 'EX', 'A', 'R', 'CV', 'SD', 'SC', 'ES', 'HO', 'DP'");
createEnumIfNotExists($db, 'convocatoria_enum', "'ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC', 'VER','ES'");
createEnumIfNotExists($db, 'jornada_enum', "'VESPERTINO', 'DIURNO'");
$db->commit();

foreach($tablas_iniciales as $tabla => $atributos) {
    try {
        $db->beginTransaction();
        
        $dropTableQuery = "DROP TABLE IF EXISTS $tabla CASCADE;";
        $db->exec($dropTableQuery);

        echo "Creando tabla $tabla si no existe...\n";
        $createTableQuery = "CREATE TABLE IF NOT EXISTS $tabla ($atributos);";
        $db->exec($createTableQuery);

        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        echo "Error al procesar la tabla $tabla: " . $e->getMessage();
    }
}

foreach($tablas_intermedias as $tabla => $atributos) {
    try {
        //echo "Eliminando tabla $tabla si existe...\n";
        $db->beginTransaction();
        
        // Elimina la tabla si existe
        $dropTableQuery = "DROP TABLE IF EXISTS $tabla CASCADE;";
        $db->exec($dropTableQuery);

        // Crea la tabla
        echo "Creando tabla $tabla si no existe...\n";
        $createTableQuery = "CREATE TABLE IF NOT EXISTS $tabla ($atributos);";
        $db->exec($createTableQuery);

        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        echo "Error al procesar la tabla intermedia $tabla: " . $e->getMessage();
    }
}
?>
