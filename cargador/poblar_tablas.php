<?php
include('../config/conexion.php');
require('parametros_tablas.php');
require('utils.php');
require('limpiador.php');

try {
    echo "INICIO DE INSERCIÓN DE DATOS\n";
    foreach ($path_tablas as $tabla_nombre => $path) {
        $file = fopen($path, 'r');
        if ($file) {
            $encabezados = fgetcsv($file, 1000,";"); // Saltar la primera línea //aweonmao
            while (($data = fgetcsv($file, 1000, ';')) !== false) { 
                // Verificar restricciones antes de insertar
                for ($i = 0; $i < count($data); $i++) {

                    if ($data[$i] == ''){ 
                        $data[$i] = remove_bom($data[$i]);
                        $data[$i] = Null; // Convertir campos vacíos en NULL, para evitar insertar datos vacíos
                        
                    }
                }
                $data = array_combine($encabezados, $data);
                //tabla esel n
                $tablas = tabla_handler($tabla_nombre, $data);
                // Realizar toda corrección necesaria antes de insertar
                foreach ($tablas as $key => &$valor) {
                    insertar_en_tabla($db, $key, $valor);
                }
            }
            echo "Terminè el archivo " . $path . "\n";
            fclose($file);

        } else {
            echo "Error al abrir el archivo $path\n";
        }    
    } 
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
    echo 'Eliminando Tabla Profesor....'."\n";
    $db->exec("DROP TABLE Profesor");
    echo 'Tabla Profesor eliminada'. "\n";
} 
catch (Exception $e) {
    echo "Error al cargar datos: " . $e->getMessage();
}
?>