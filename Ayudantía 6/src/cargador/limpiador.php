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
        'Estudiante' => [],
        'Persona' => [],
        'Bloqueo' => [],
        'UltimoLogro' => []
    ];

    $datos_malos = [
        'Estudiante' => [],
        'Persona' => [],
        'Bloqueo' => [],
        'UltimoLogro' => []
    ];
    
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
                $datos_malos['Estudiante']['run'] = $valor;
                $datos_malos['Persona']['run'] = $valor;
            }
            elseif (is_numeric($valor)){
                $valor = (int)$valor;
                $tablas['Estudiante']['run'] = $valor;
                $tablas['Persona']['run'] = $valor;
            }
        }
        else if ($key == "Numero de Alumno"){
            if (is_numeric($valor)){
                $valor = (int)$valor;
                $tablas['Estudiante']['numero_estudiante'] = $valor;
                $tablas['Bloqueo']['numero_estudiante'] = $valor;
                $tablas['UltimoLogro']['numero_estudiante'] = $valor;
            }
            else{
                $datos_malos['Estudiante']['numero_estudiante'] = $valor;
                $datos_malos['Bloqueo']['numero_estudiante'] = $valor;
                $datos_malos['UltimoLogro']['numero_estudiante'] = $valor;
            }
        }
        else if ($key == "DV"){
            if ((is_numeric($valor) || strtoupper($valor) === 'K' ) && (gettype($valor) === CHAR(1))) {
                $tablas['Estudiante']['dv'] = $valor;
                $tablas['Persona']['dv'] = $valor;
            }
            else{
                $datos_malos['Estudiante']['dv'] = $valor;
                $datos_malos['Persona']['dv'] = $valor;
            }
        }

        else if ($key == "Nombres"){
            if (is_string($valor) && is_string($data[''])){
                $valor = $valor + " " + $data[''];
            }
            else {
                $datos_malos['Estudiante']['nombres'] = $valor;
                $datos_malos['Persona']['nombres'] = $valor;
                $valor = null;
            }
            $tablas['Estudiante']['nombres'] = $valor;
            $tablas['Persona']['nombres'] = $valor;
        }

        else if ($key == "Primer Apellido"){
            
            if (is_string($valor)){
                $tablas['Estudiante']['apellido_paterno'] = $valor;
                $tablas['Persona']['apellido_materno'] = $valor;
            }
        }

        else if ($key == "Cohorte"){
            
            if (!is_string($valor)){
                $datos_malos['Estudiante']['cohorte'] = $valor;
            }
            $tablas['Estudiante']['cohorte'] = $valor;
        }
        return $tablas;
    }
    //return ["valores_malos" =>[],
    //"estudiantes" => ,
    //"Carreras" => ]
}

function limpiar_asignatura($data){
    $tablas = [
        'IncluyeCurso' => [],
        'Curso' => [],
        'CursoPrerequisito' => [],
    ];
    $datos_malos = [
        'IncluyeCurso' => [],
        'Curso' => [],
    ];
    foreach ($data as $key => &$valor) {
        if ($key == 'Plan'){
            if (strlen($valor) < 30){
                $tablas['IncluyeCurso']['codigo_plan'] = $valor; 
            }
            else{
                $datos_malos['IncluyeCurso']['codigo_plan'] = $valor; 
            }
        }
        else if ($key == "Asignatura id"){
            if (strlen($valor) < 10){
                $tablas['Curso']['sigla_curso'] = $valor;
            }
            else{
                $datos_malos['Curso']['sigla_curso'] = $valor;
            }
        }
        else if ($key == "Asignatura"){
            if (is_str($valor) && strlen($valor) < 100){
                $tablas['Curso']['nombre_curso'] = $valor;
            }else{
                $datos_malos['Curso']['nombre_curso'] = $valor;
            }
        }
        else if ($key == "Nivel"){
            if (is_numeric($valor)){
                $valor_numero = (int)$valor;
                $tablas['Curso']['nivel'] = $valor_numero;
            }else{
                $datos_malos['Curso']['nivel'] = $valor;
            }
        }
        else if ($key == "Prerequisito"){
            if (gettype($valor) === CHAR(1)){
                $tablas['Curso']['prerequisito'] = $valor;
            }else{
                $datos_malos['Curso']['prerequisito'] = $valor;
            }
        }
    }
}


function limpiar_planes($data){
    // exequiel
    $tablas = [
        'Facultad' => [],
        'Carrera' => [],
        'PlanEstudio' => [],
        ''];

    $datos_malos = [
        'Facultad' => [],
        'Carrera' => [],
        'PlanEstudio' => [],
        ''];

    foreach ($data as $key => &$valor) {
        if ($key == 'Código Plan') {
            if (!is_string($key)) {
                $datos_malos['PlanEstudio']['codigo_plan'] = $valor;
                $valor = NULL;
            }
            $tablas['PlanEstudio']['codigo_plan'] = $valor;
        }
        elseif ($key == "Facultad"){
            if (is_string($key)) {
                $tablas['Facultad']['nombre'] = $valor;
            }
            else{
                $datos_malos['Facultad']['nombre'] = $valor;
            }
        }
        elseif ($key == "Carrera"){
            if (is_string($key)) {
                $tablas['Carrera']['nombre'] = $valor;
            }
            else{
                $datos_malos['Carrera']['nombre'] = $valor;
            }
        }
        elseif ($key == "Plan"){
            if (is_string($key)) {
                $tablas['PlanEstudio']['nombre'] = $valor;
            }
            else{
                $datos_malos['PlanEstudio']['nombre'] = $valor;
            }
        }
        elseif ($key == ""){
            
        }
        elseif ($key == ""){
            
        }
        elseif ($key == ""){
            
        }        
    }
}

function limpiar_profesor(){
    // joaco:
}
?>