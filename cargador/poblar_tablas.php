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
            $header = fgetcsv($file, 1000,";");
            while (($data = fgetcsv($file, 1000, ';')) !== false) { 
                for ($i = 0; $i < count($data); $i++) {

                    if ($data[$i] == ''){ 
                        $data[$i] = remove_bom($data[$i]);
                        $data[$i] = Null; 
                        
                    }
                }
                $data = array_combine($header, $data);
                $tablas = tabla_handler($tabla_nombre, $data);
                foreach ($tablas as $key => &$valor) {
                    insertar_en_tabla($db, $key, $valor);
                }
            }
            echo "Terminé el archivo " . $path . "\n";
            fclose($file);

        } else {
            echo "Error al abrir el archivo $path\n";
        }    
    }

}
catch (Exception $e) {
    echo "Error al cargar datos: " . $e->getMessage();
}
?>