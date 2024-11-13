<?php
function tabla_eliminator($db) {
    $atributo = 'run';
    $db->exec("DELETE FROM Persona WHERE $atributo = -1;");
    echo $atributo." nulos eliminados.\n";
    $atributo = 'numero_estudiante';
    $db->exec("DELETE FROM Estudiante WHERE $atributo = -1;");
    echo $atributo." nulos eliminados.\n";
    $atributo = 'codigo_plan';
    $db->exec("DELETE FROM PlanEstudio WHERE $atributo = 'NULL';");
    echo $atributo." nulos eliminados.\n";
    $atributo = 'sigla_curso';
    $db->exec("DELETE FROM Curso WHERE $atributo = '-1';");
    echo $atributo." nulos eliminados.\n";
    $atributo = 'nombre_facultad';
    $db->exec("DELETE FROM Facultad WHERE $atributo = 'NULL';");
    echo $atributo." nulos eliminados.\n";
    $atributo = 'codigo_departamento';
    $db->exec("DELETE FROM Departamento WHERE $atributo = -1;");
    echo $atributo." nulos eliminados.\n";
}
?>