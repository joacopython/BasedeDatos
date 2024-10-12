<?php
function typeConvert($valor){
    if (is_numeric($valor)){
        if (strpos($valor, ".") !== false){
            return (float)$valor;
        }
        return (int)$valor;
    }
    
    $valor_minusc = strtolower($valor);

    if ($valor_minusc === "true") {
        return true;
    }
    elseif ($valor_minusc === "false") {
        return false;
    }

    return $valor;
}

function array_to_csv($array, $file_name) {
    // Abrir el archivo para escritura
    $file = fopen($file_name, 'w');

    if ($file === false) {
        return false; // Error al abrir el archivo
    }

    // Obtener los encabezados desde la primera fila del array
    $headers = array_map('trim', array_keys($array[0]));

    // Escribir los encabezados en el archivo
    fputcsv($file, $headers, ";");

    // Escribir cada fila de datos en el archivo
    foreach ($array as $row) {
        fputcsv($file, $row, ";");
    }

    // Cerrar el archivo
    fclose($file);
    
    return true; // Retornar éxito
}

function tabla_handler($tabla, $data){
    //$data[0] = "2024-1"
    //$data["Periodo"] = "2024-1"

}



function limpiar_estudiantes($data){
    $tablas = [
        'Estudiantes' => [],
        'Personas' => [],
        'Bloqueos' => [],
        '']
    foreach ($data as $key => &$valor) {
        if ($key == "Bloqueo"){
            if ($valor == "N"){
                $valor = false;
            }
            else if ($valor == "S"){
                $valor = true;
            }
            else{
                $valor = null;
            }
        }
        else if ($key == "Causal Bloqueo"){
            
        }
        else if ($key == "RUN"){
            if (!is_numeric($valor)){
                //pasar a datos malos
            }
            elseif (is_numeric($valor)){
                $valor = (int)$valor;
            }
        }
        else if ($key == "Numero de Alumno"){
            if (!is_numeric($valor)){
                //pasar a datos malos
            }
            elseif (is_numeric($valor)){
                $valor = (int)$valor;
            }
        }
        else if ($key == "DV"){
            if (gettype($valor) != CHAR(1)){
                //pasar a datos malos
            }
        }

        else if ($key == "Nombres"){
            $valor = $valor + " " + $data[''];
        }

        else if ($key == "Primer Apellido"){

        }
        return $tablas;
    }
    //return ["valores_malos" =>[],
    //"estudiantes" => ,
    //"Carreras" => ]
}

function limpiar_curso($data){
    $datos_malos = [];
    foreach ($data as $key => &$valor) {
        if ($key == 'Nivel'){
            if (gettype($valor) != INT){
                #pasar a datos malos
            }
        }
        else if ($key == ""){

        }
        else if ($key == ""){

        }
        else if ($key == ""){

        }
        else if ($key == ""){

        }
        else if ($key == ""){

        }
    }
}

function limpiar_asignatura(){

}


function limpiar_planes($data){
    // exequiel
    foreach ($data as $key => &$valor) {
        if ($key == 'Codigo Plan') {            
        }
        
    }
}

function limpiar_profesor(){
    // joaco:
}
?>