<?php
include('../config/conexion.php');

try {
    echo "INICIO DE ELIMINACIÓN DE DATOS";
    $atributo = 'run';
    $db->exec("DELETE FROM Persona WHERE $atributo = -1;");
    echo $atributo . " nulos eliminados.\n";
    
    $atributo = 'numero_estudiante';
    $db->exec("DELETE FROM Estudiante WHERE $atributo = -1;");
    echo $atributo . " nulos eliminados.\n";
    
    $atributo = 'codigo_plan';
    $db->exec("DELETE FROM PlanEstudio WHERE $atributo = 'NULL';");
    echo $atributo . " nulos eliminados.\n";
    
    $atributo = 'sigla_curso';
    $db->exec("DELETE FROM Curso WHERE $atributo = '-1';");
    echo $atributo . " nulos eliminados.\n";
    
    $atributo = 'nombre_facultad';
    $db->exec("DELETE FROM Facultad WHERE $atributo = 'NULL';");
    echo $atributo . " nulos eliminados.\n";
    
    $atributo = 'codigo_departamento';
    $db->exec("DELETE FROM Departamento WHERE $atributo = -1;");
    echo $atributo . " nulos eliminados.\n";

    echo "Elminando tabla Profesor...\n";
    $db->exec("DROP TABLE Profesor ;");
    echo "Tabla Profesor Eliminada\n";

    
    echo "Eliminando las notas de los cursos del periodo 2024-02...\n";
    $db->exec("DELETE FROM HistorialAcademico WHERE periodo = '2024-02'");
    echo "Notas eliminadas correctamente \n";

} catch (PDOException $e) {
    echo "Ocurrió un error al intentar eliminar registros: " . $e->getMessage() . "\n";
}
?>